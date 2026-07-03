@extends('layouts.careers')

@section('title', 'Apply for ' . $job->title)
@section('meta_description', 'Apply for the ' . $job->title . ' position at ' . config('recruitment.seo.company_name'))

@push('styles')
<style>
    .step-indicator { transition: all 0.3s ease; }
    .step-active { background-color: #0f766e; color: white; border-color: #0f766e; }
    .step-completed { background-color: #d1fae5; color: #065f46; border-color: #10b981; }
    .step-pending { background-color: #f3f4f6; color: #6b7280; border-color: #d1d5db; }
    .form-section { display: none; animation: fadeIn 0.4s ease; }
    .form-section.active { display: block; }
    .file-drop-zone { border: 2px dashed #d1d5db; transition: all 0.2s; }
    .file-drop-zone:hover, .file-drop-zone.drag-over { border-color: #0f766e; background-color: #f0fdfa; }
</style>
@endpush

@section('content')
<section class="gradient-hero py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('careers.jobs.show', $job) }}" class="inline-flex items-center text-white/80 hover:text-white mb-4 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Job Details
        </a>
        <h1 class="text-3xl font-bold text-white">Apply for {{ $job->title }}</h1>
        <p class="text-white/80 mt-2">{{ $job->department->name }} &middot; {{ $job->location->city }}</p>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            <p class="font-medium mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between relative">
                <div class="absolute left-0 right-0 top-1/2 h-0.5 bg-gray-200 -z-10"></div>
                @php $steps = ['Personal', 'Contact', 'Education', 'Experience', 'Skills', 'Profiles', 'Documents', 'Screening', 'Review']; @endphp
                @foreach($steps as $index => $step)
                <div class="step-indicator w-10 h-10 rounded-full border-2 flex items-center justify-center text-sm font-medium {{ $index === 0 ? 'step-active' : 'step-pending' }}"
                    id="step-indicator-{{ $index }}" data-step="{{ $index }}">
                    {{ $index + 1 }}
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2">
                @foreach($steps as $step)
                <span class="text-xs text-gray-500 w-10 text-center">{{ $step }}</span>
                @endforeach
            </div>
        </div>

        <form action="{{ route('careers.apply.store', $job) }}" method="POST" enctype="multipart/form-data" id="application-form">
            @csrf

            <!-- Step 1: Personal Information -->
            <div class="form-section active" id="step-0">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Personal Information</h2>
                    <p class="text-gray-500 text-sm mb-6">Tell us about yourself</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" name="first_name" required value="{{ old('first_name') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" name="last_name" required value="{{ old('last_name') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white">
                                <option value="">Select</option>
                                @foreach(App\Models\Candidate::GENDERS as $key => $label)
                                <option value="{{ $key }}" {{ old('gender') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                            <input type="text" name="nationality" value="{{ old('nationality') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
                            <select name="marital_status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white">
                                <option value="">Select</option>
                                @foreach(App\Models\Candidate::MARITAL_STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ old('marital_status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Contact Information -->
            <div class="form-section" id="step-1">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Contact Information</h2>
                    <p class="text-gray-500 text-sm mb-6">How can we reach you?</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                            <input type="tel" name="phone" required value="{{ old('phone') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Phone</label>
                            <input type="tel" name="alternate_phone" value="{{ old('alternate_phone') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Address</label>
                        <textarea name="current_address" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">{{ old('current_address') }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Permanent Address</label>
                        <textarea name="permanent_address" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">{{ old('permanent_address') }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" name="state" value="{{ old('state') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" name="country" value="{{ old('country') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Education -->
            <div class="form-section" id="step-2">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Education</h2>
                    <p class="text-gray-500 text-sm mb-6">Tell us about your educational background</p>
                    <div id="education-container">
                        @php $educations = old('education', [[]]); @endphp
                        @foreach($educations as $i => $edu)
                        <div class="education-entry border border-gray-200 rounded-lg p-4 mb-4" data-index="{{ $i }}">
                            <div class="flex justify-between mb-3">
                                <h4 class="font-medium text-gray-700">Education #{{ $i + 1 }}</h4>
                                @if($i > 0)
                                <button type="button" class="text-red-500 hover:text-red-700 text-sm remove-education">Remove</button>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Degree Type</label>
                                    <select name="education[{{ $i }}][degree_type]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 bg-white">
                                        <option value="">Select</option>
                                        @foreach(App\Models\CandidateEducation::DEGREE_TYPES as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Degree Name *</label>
                                    <input type="text" name="education[{{ $i }}][degree_name]" placeholder="e.g., Bachelor of Science"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
                                    <input type="text" name="education[{{ $i }}][field_of_study]" placeholder="e.g., Computer Science"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Institution *</label>
                                    <input type="text" name="education[{{ $i }}][institution]" placeholder="e.g., Stanford University"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                                    <input type="number" name="education[{{ $i }}][start_year]" min="1950" max="{{ date('Y') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                                    <input type="number" name="education[{{ $i }}][end_year]" min="1950" max="{{ date('Y') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-education" class="text-brand-600 hover:text-brand-700 font-medium text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Another Education
                    </button>
                </div>
            </div>

            <!-- Step 4: Experience -->
            <div class="form-section" id="step-3">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Work Experience</h2>
                    <p class="text-gray-500 text-sm mb-6">Tell us about your professional journey</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Company</label>
                            <input type="text" name="current_company" value="{{ old('current_company') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Designation</label>
                            <input type="text" name="current_designation" value="{{ old('current_designation') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Experience (years)</label>
                            <input type="number" name="total_experience_years" step="0.5" min="0" max="50" value="{{ old('total_experience_years') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notice Period</label>
                            <select name="notice_period" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 bg-white">
                                <option value="">Select</option>
                                @foreach(App\Models\Candidate::NOTICE_PERIODS as $key => $label)
                                <option value="{{ $key }}" {{ old('notice_period') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Salary (USD)</label>
                            <input type="number" name="current_salary" min="0" value="{{ old('current_salary') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected Salary (USD)</label>
                            <input type="number" name="expected_salary" min="0" value="{{ old('expected_salary') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <h3 class="font-medium text-gray-700 mb-4">Previous Experience</h3>
                    <div id="experience-container">
                        @php $experiences = old('experience', [[]]); @endphp
                        @foreach($experiences as $i => $exp)
                        <div class="experience-entry border border-gray-200 rounded-lg p-4 mb-4" data-index="{{ $i }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                    <input type="text" name="experience[{{ $i }}][company_name]"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                                    <input type="text" name="experience[{{ $i }}][designation]"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="date" name="experience[{{ $i }}][start_date]"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="date" name="experience[{{ $i }}][end_date]"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-experience" class="text-brand-600 hover:text-brand-700 font-medium text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Another Experience
                    </button>
                </div>
            </div>

            <!-- Step 5: Skills -->
            <div class="form-section" id="step-4">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Skills</h2>
                    <p class="text-gray-500 text-sm mb-6">Highlight your key skills and competencies</p>
                    <div id="skills-container">
                        @php $skills = old('skills', [['name' => ''], ['name' => ''], ['name' => '']]); @endphp
                        @foreach($skills as $i => $skill)
                        <div class="skill-entry flex gap-4 mb-3" data-index="{{ $i }}">
                            <input type="text" name="skills[{{ $i }}][name]" value="{{ $skill['name'] ?? '' }}" placeholder="e.g., JavaScript"
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                            <select name="skills[{{ $i }}][proficiency]" class="w-40 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 bg-white">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate" selected>Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-skill" class="text-brand-600 hover:text-brand-700 font-medium text-sm flex items-center mt-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add More Skills
                    </button>
                </div>
            </div>

            <!-- Step 6: Online Profiles -->
            <div class="form-section" id="step-5">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Online Profiles</h2>
                    <p class="text-gray-500 text-sm mb-6">Share your professional online presence</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
                            <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">GitHub</label>
                            <input type="url" name="github_url" value="{{ old('github_url') }}" placeholder="https://github.com/..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Portfolio</label>
                            <input type="url" name="portfolio_url" value="{{ old('portfolio_url') }}" placeholder="https://..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="url" name="website_url" value="{{ old('website_url') }}" placeholder="https://..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 7: Documents -->
            <div class="form-section" id="step-6">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Documents</h2>
                    <p class="text-gray-500 text-sm mb-6">Upload your resume and other supporting documents</p>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resume / CV *</label>
                            <div class="file-drop-zone rounded-xl p-8 text-center cursor-pointer" onclick="document.getElementById('resume').click()">
                                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required class="hidden"
                                    onchange="updateFileLabel(this, 'resume-label')">
                                <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p class="text-gray-600 font-medium" id="resume-label">Click to upload resume</p>
                                <p class="text-gray-400 text-sm mt-1">PDF, DOC, or DOCX up to 10MB</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Letter (Optional)</label>
                            <div class="file-drop-zone rounded-xl p-6 text-center cursor-pointer" onclick="document.getElementById('cover_letter').click()">
                                <input type="file" id="cover_letter" name="cover_letter" accept=".pdf,.doc,.docx" class="hidden"
                                    onchange="updateFileLabel(this, 'cover-label')">
                                <p class="text-gray-600 font-medium" id="cover-label">Click to upload cover letter</p>
                                <p class="text-gray-400 text-sm mt-1">PDF, DOC, or DOCX</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Joining Date</label>
                        <input type="date" name="expected_joining_date" value="{{ old('expected_joining_date') }}"
                            class="w-full md:w-1/2 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            <!-- Step 8: Screening Questions -->
            <div class="form-section" id="step-7">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Screening Questions</h2>
                    <p class="text-gray-500 text-sm mb-6">Please answer the following questions</p>

                    @forelse($job->screeningQuestions as $index => $question)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $question->question }}
                            @if($question->is_required)
                            <span class="text-red-500">*</span>
                            @endif
                        </label>
                        @if($question->type === 'text')
                            <input type="text" name="screening_answers[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        @elseif($question->type === 'textarea')
                            <textarea name="screening_answers[{{ $question->id }}]" rows="4" {{ $question->is_required ? 'required' : '' }}
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500"></textarea>
                        @elseif($question->type === 'yes_no')
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" name="screening_answers[{{ $question->id }}]" value="yes" {{ $question->is_required ? 'required' : '' }} class="mr-2">
                                    Yes
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="screening_answers[{{ $question->id }}]" value="no" class="mr-2">
                                    No
                                </label>
                            </div>
                        @elseif($question->type === 'single_choice')
                            <select name="screening_answers[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 bg-white">
                                <option value="">Select</option>
                                @foreach($question->options ?? [] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @elseif($question->type === 'number')
                            <input type="number" name="screening_answers[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        @endif
                    </div>
                    @empty
                    <p class="text-gray-500">No screening questions for this position.</p>
                    @endforelse
                </div>
            </div>

            <!-- Step 9: Review & Submit -->
            <div class="form-section" id="step-8">
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Review & Submit</h2>
                    <p class="text-gray-500 text-sm mb-6">Please review your application before submitting</p>

                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <p class="text-amber-800 text-sm">You're applying for <strong>{{ $job->title }}</strong> in the <strong>{{ $job->department->name }}</strong> department.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Application Source</h3>
                        <select name="source" class="w-full md:w-1/2 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 bg-white">
                            @foreach(App\Models\Candidate::SOURCES as $key => $label)
                            <option value="{{ $key }}" {{ old('source', 'careers_page') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-start">
                            <input type="checkbox" name="terms_accepted" required class="mt-1 mr-3 w-4 h-4 text-brand-600 rounded focus:ring-brand-500">
                            <span class="text-sm text-gray-600">I confirm that all the information provided is accurate and complete to the best of my knowledge. *</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="privacy_accepted" required class="mt-1 mr-3 w-4 h-4 text-brand-600 rounded focus:ring-brand-500">
                            <span class="text-sm text-gray-600">I have read and agree to the <a href="#" class="text-brand-600 hover:underline">Privacy Policy</a> and consent to the processing of my personal data for recruitment purposes. *</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-8">
                <button type="button" id="prev-btn" class="hidden bg-white border border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition">
                    Previous
                </button>
                <div class="flex-1"></div>
                <button type="button" id="next-btn" class="bg-brand-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-brand-700 transition">
                    Next Step
                </button>
                <button type="submit" id="submit-btn" class="hidden bg-brand-600 text-white px-8 py-2.5 rounded-lg font-medium hover:bg-brand-700 transition">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
let currentStep = 0;
const totalSteps = 9;

function updateStepIndicators() {
    for (let i = 0; i < totalSteps; i++) {
        const indicator = document.getElementById(`step-indicator-${i}`);
        indicator.classList.remove('step-active', 'step-completed', 'step-pending');
        if (i === currentStep) {
            indicator.classList.add('step-active');
        } else if (i < currentStep) {
            indicator.classList.add('step-completed');
        } else {
            indicator.classList.add('step-pending');
        }
    }
}

function showStep(step) {
    document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
    document.getElementById(`step-${step}`).classList.add('active');

    document.getElementById('prev-btn').classList.toggle('hidden', step === 0);
    document.getElementById('next-btn').classList.toggle('hidden', step === totalSteps - 1);
    document.getElementById('submit-btn').classList.toggle('hidden', step !== totalSteps - 1);

    currentStep = step;
    updateStepIndicators();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('next-btn').addEventListener('click', () => {
    const currentSection = document.getElementById(`step-${currentStep}`);
    const requiredInputs = currentSection.querySelectorAll('[required]');
    let stepIsValid = true;

    for (const input of requiredInputs) {
        if (!input.checkValidity()) {
            input.reportValidity();
            stepIsValid = false;
            break;
        }
    }

    if (stepIsValid && currentStep < totalSteps - 1) {
        showStep(currentStep + 1);
    }
});

document.getElementById('prev-btn').addEventListener('click', () => {
    if (currentStep > 0) showStep(currentStep - 1);
});

function updateFileLabel(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        label.classList.add('text-brand-700', 'font-semibold');
    }
}

// Dynamic add/remove education
document.getElementById('add-education')?.addEventListener('click', function() {
    const container = document.getElementById('education-container');
    const count = container.querySelectorAll('.education-entry').length;
    const template = container.querySelector('.education-entry').cloneNode(true);
    template.setAttribute('data-index', count);
    template.querySelector('h4').textContent = `Education #${count + 1}`;
    template.querySelectorAll('input, select').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace(/education\[\d+\]/, `education[${count}]`));
        }
        input.value = '';
    });
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'text-red-500 hover:text-red-700 text-sm remove-education';
    removeBtn.textContent = 'Remove';
    removeBtn.addEventListener('click', function() { template.remove(); });
    const header = template.querySelector('.flex.justify-between');
    if (header.querySelector('.remove-education')) {
        header.querySelector('.remove-education').remove();
    }
    header.appendChild(removeBtn);
    container.appendChild(template);
});

document.getElementById('add-experience')?.addEventListener('click', function() {
    const container = document.getElementById('experience-container');
    const count = container.querySelectorAll('.experience-entry').length;
    const template = container.querySelector('.experience-entry').cloneNode(true);
    template.setAttribute('data-index', count);
    template.querySelectorAll('input').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace(/experience\[\d+\]/, `experience[${count}]`));
        }
        input.value = '';
    });
    container.appendChild(template);
});

document.getElementById('add-skill')?.addEventListener('click', function() {
    const container = document.getElementById('skills-container');
    const count = container.querySelectorAll('.skill-entry').length;
    const template = container.querySelector('.skill-entry').cloneNode(true);
    template.setAttribute('data-index', count);
    template.querySelectorAll('input, select').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace(/skills\[\d+\]/, `skills[${count}]`));
        }
        input.value = input.tagName === 'SELECT' ? 'intermediate' : '';
    });
    container.appendChild(template);
});

// Initialize
updateStepIndicators();
</script>
@endpush
