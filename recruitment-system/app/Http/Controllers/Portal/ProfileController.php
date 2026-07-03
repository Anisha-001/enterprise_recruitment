<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Show candidate profile edit form.
     */
    public function edit(): View
    {
        $candidate = Auth::guard('candidate')->user();
        return view('portal.profile.edit', compact('candidate'));
    }

    /**
     * Update candidate profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $candidate = Auth::guard('candidate')->user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female,non_binary,prefer_not_to_say'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'string', 'in:single,married,divorced,widowed,separated'],
            'current_address' => ['nullable', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            
            // Professional
            'current_company' => ['nullable', 'string', 'max:150'],
            'current_designation' => ['nullable', 'string', 'max:150'],
            'current_salary' => ['nullable', 'numeric', 'min:0'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'notice_period' => ['nullable', 'string', 'in:immediate,15_days,30_days,60_days,90_days,more_than_90'],
            'total_experience_years' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'highest_qualification' => ['nullable', 'string', 'max:200'],
            'university' => ['nullable', 'string', 'max:200'],
            'passing_year' => ['nullable', 'integer', 'min:1950', 'max:' . (date('Y') + 5)],
            
            // Social Links
            'linkedin_url' => ['nullable', 'url', 'max:500'],
            'github_url' => ['nullable', 'url', 'max:500'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'behance_url' => ['nullable', 'url', 'max:500'],
            'dribbble_url' => ['nullable', 'url', 'max:500'],

            // Photograph
            'photograph' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $data = $request->except(['photograph']);

        try {
            // Process photograph upload
            if ($request->hasFile('photograph')) {
                // Delete old photograph if exists
                if ($candidate->photograph && Storage::disk('public')->exists($candidate->photograph)) {
                    Storage::disk('public')->delete($candidate->photograph);
                }

                $path = $request->file('photograph')->store('candidates/' . $candidate->id . '/photos', 'public');
                $data['photograph'] = $path;
            }

            $candidate->update($data);

            Log::info('Candidate updated profile details successfully', ['candidate_id' => $candidate->id]);

            return back()->with('success', 'Profile updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update candidate profile', [
                'candidate_id' => $candidate->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Update candidate password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $candidate = Auth::guard('candidate')->user();

        $request->validate([
            'current_password' => ['required', 'current_password:candidate'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $candidate->update([
                'password' => Hash::make($request->password),
            ]);

            Log::info('Candidate changed password successfully', ['candidate_id' => $candidate->id]);

            return back()->with('success', 'Password updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update candidate password', [
                'candidate_id' => $candidate->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to update password. Please try again.');
        }
    }
}
