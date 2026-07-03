<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Application;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Location;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\EmailLog;
use App\Mail\ScreeningStartedMail;
use App\Mail\RejectionMail;
use App\Services\Application\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CandidatePortalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Department $department;
    private Designation $designation;
    private Location $location;
    private JobPosting $jobPosting;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard pre-requisites
        $this->user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-001',
            'status' => 'active',
            'is_admin' => true,
        ]);

        $this->department = Department::create([
            'name' => 'Engineering',
            'code' => 'ENG',
            'slug' => 'engineering',
            'status' => 'active',
        ]);

        $this->designation = Designation::create([
            'name' => 'Software Engineer',
            'code' => 'SE',
            'slug' => 'software-engineer',
            'department_id' => $this->department->id,
            'status' => 'active',
        ]);

        $this->location = Location::create([
            'name' => 'HQ office',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'status' => 'active',
        ]);

        $this->jobPosting = JobPosting::create([
            'title' => 'PHP Developer',
            'slug' => 'php-developer',
            'description' => 'Great PHP job',
            'department_id' => $this->department->id,
            'designation_id' => $this->designation->id,
            'location_id' => $this->location->id,
            'employment_type' => 'full_time',
            'experience_level' => 'mid',
            'created_by' => $this->user->id,
            'status' => 'published',
        ]);
    }

    /**
     * Test a candidate cannot view another candidate's data.
     */
    public function test_candidate_cannot_view_another_candidates_data(): void
    {
        $candidateA = Candidate::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);

        $candidateB = Candidate::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
            'password' => bcrypt('password'),
        ]);

        $applicationA = Application::create([
            'candidate_id' => $candidateA->id,
            'job_posting_id' => $this->jobPosting->id,
            'status' => 'new',
        ]);

        // Act as Candidate B
        $response = $this->actingAs($candidateB, 'candidate')
            ->get(route('candidate.applications.show', $applicationA->id));

        $response->assertStatus(403);
    }

    /**
     * Test set-password link works and expires correctly.
     */
    public function test_set_password_link_works_and_expires(): void
    {
        $candidate = Candidate::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);

        // Generate signed URL
        $url = URL::temporarySignedRoute(
            'candidate.set-password',
            now()->addHours(24),
            ['email' => $candidate->email]
        );

        // Access works
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertViewIs('portal.auth.set-password');

        // Post password works
        $postResponse = $this->post($url, [
            'email' => $candidate->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $postResponse->assertRedirect(route('candidate.dashboard'));
        $this->assertTrue(Hash::check('newpassword123', $candidate->fresh()->password));
        $this->assertNotNull($candidate->fresh()->email_verified_at);

        // Log out candidate first to avoid RedirectIfAuthenticated middleware redirecting to homepage
        \Illuminate\Support\Facades\Auth::guard('candidate')->logout();

        // Move time past expiry
        $expiredUrl = URL::temporarySignedRoute(
            'candidate.set-password',
            now()->subHours(1),
            ['email' => $candidate->email]
        );

        $expiredResponse = $this->get($expiredUrl);
        $expiredResponse->assertRedirect(route('candidate.login'));
    }

    /**
     * Test changing status triggers exactly one email.
     */
    public function test_changing_application_status_triggers_email_notification(): void
    {
        Mail::fake();

        $candidate = Candidate::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);

        $application = Application::create([
            'candidate_id' => $candidate->id,
            'job_posting_id' => $this->jobPosting->id,
            'status' => 'new',
        ]);

        // Act as Admin User for Auth logging
        $this->actingAs($this->user);

        // Trigger transition
        app(ApplicationService::class)->transitionStatus($application, 'screening', 'Moving to screening', $this->user->id);

        // Assert mail was queued to correct candidate
        Mail::assertQueued(ScreeningStartedMail::class, function ($mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });

        // Assert exactly 1 email logged in email_logs
        $this->assertDatabaseHas('email_logs', [
            'emailable_type' => Application::class,
            'emailable_id' => $application->id,
            'recipient_email' => $candidate->email,
            'template' => 'status_change_screening',
        ]);
    }

    /**
     * Test rejected status doesn't leak internal rejection_reason if config disables it.
     */
    public function test_rejected_status_does_not_leak_reason_when_disabled(): void
    {
        Mail::fake();
        Config::set('recruitment.notifications.show_rejection_reason_to_candidate', false);

        $candidate = Candidate::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);

        $application = Application::create([
            'candidate_id' => $candidate->id,
            'job_posting_id' => $this->jobPosting->id,
            'status' => 'new',
        ]);

        $this->actingAs($this->user);

        // Move to screening and then rejected (valid transition check)
        $application->update(['status' => 'screening']);
        app(ApplicationService::class)->transitionStatus($application, 'rejected', 'Failed technical coding test', $this->user->id);

        Mail::assertQueued(RejectionMail::class, function ($mail) {
            return $mail->rejectionReason === null;
        });

        // Enable config and check if it gets passed
        Config::set('recruitment.notifications.show_rejection_reason_to_candidate', true);
        
        $candidate2 = Candidate::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '9876543210',
        ]);

        $application2 = Application::create([
            'candidate_id' => $candidate2->id,
            'job_posting_id' => $this->jobPosting->id,
            'status' => 'screening',
        ]);

        app(ApplicationService::class)->transitionStatus($application2, 'rejected', 'Failed interview', $this->user->id);

        Mail::assertQueued(RejectionMail::class, function ($mail) {
            return $mail->rejectionReason === 'Failed interview';
        });
    }
}
