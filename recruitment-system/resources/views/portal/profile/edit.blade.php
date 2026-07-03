@extends('layouts.portal')

@section('title', 'My Profile')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">My Profile</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your personal and professional profile details, photograph, and password.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2-Column: Details Update Forms -->
        <div class="lg:col-span-2 space-y-8">
            <form action="{{ route('candidate.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Personal Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
                    <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Personal Information</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="first_name" name="first_name" required value="{{ old('first_name', $candidate->first_name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('first_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $candidate->middle_name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('middle_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required value="{{ old('last_name', $candidate->last_name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" id="phone" name="phone" required value="{{ old('phone', $candidate->phone) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="alternate_phone" class="block text-sm font-medium text-gray-700">Alternate Phone</label>
                            <input type="text" id="alternate_phone" name="alternate_phone" value="{{ old('alternate_phone', $candidate->alternate_phone) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('alternate_phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <select id="gender" name="gender" class="mt-1 block w-full border border-gray-300 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                <option value="">Select...</option>
                                @foreach(\App\Models\Candidate::GENDERS as $val => $label)
                                    <option value="{{ $val }}" {{ old('gender', $candidate->gender) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $candidate->date_of_birth ? $candidate->date_of_birth->format('Y-m-d') : '') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('date_of_birth') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality</label>
                            <input type="text" id="nationality" name="nationality" value="{{ old('nationality', $candidate->nationality) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" id="city" name="city" value="{{ old('city', $candidate->city) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State / Region</label>
                            <input type="text" id="state" name="state" value="{{ old('state', $candidate->state) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                            <input type="text" id="country" name="country" value="{{ old('country', $candidate->country) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>
                </div>

                <!-- Professional Details Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
                    <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Professional Information</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="current_company" class="block text-sm font-medium text-gray-700">Current Company</label>
                            <input type="text" id="current_company" name="current_company" value="{{ old('current_company', $candidate->current_company) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label for="current_designation" class="block text-sm font-medium text-gray-700">Current Designation</label>
                            <input type="text" id="current_designation" name="current_designation" value="{{ old('current_designation', $candidate->current_designation) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label for="total_experience_years" class="block text-sm font-medium text-gray-700">Experience (Years)</label>
                            <input type="number" step="0.1" id="total_experience_years" name="total_experience_years" value="{{ old('total_experience_years', $candidate->total_experience_years) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('total_experience_years') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="notice_period" class="block text-sm font-medium text-gray-700">Notice Period</label>
                            <select id="notice_period" name="notice_period" class="mt-1 block w-full border border-gray-300 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                <option value="">Select...</option>
                                @foreach(\App\Models\Candidate::NOTICE_PERIODS as $val => $label)
                                    <option value="{{ $val }}" {{ old('notice_period', $candidate->notice_period) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="highest_qualification" class="block text-sm font-medium text-gray-700">Highest Qualification</label>
                            <input type="text" id="highest_qualification" name="highest_qualification" value="{{ old('highest_qualification', $candidate->highest_qualification) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>
                </div>

                <!-- Social Profiles Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
                    <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Social Profiles</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="linkedin_url" class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                            <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $candidate->linkedin_url) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('linkedin_url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="github_url" class="block text-sm font-medium text-gray-700">GitHub URL</label>
                            <input type="url" id="github_url" name="github_url" value="{{ old('github_url', $candidate->github_url) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                            @error('github_url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2.5 px-6 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-brand-500/10 focus:outline-none transition">
                        Save Changes
                    </button>
                </div>

            </form>
        </div>

        <!-- Right Side: Profile Photo & Password Blocks -->
        <div class="space-y-8">
            <!-- Profile Photo Update Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 font-mono">Profile Photograph</h2>
                
                <form action="{{ route('candidate.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden inputs so validation doesn't complain about missing fields -->
                    <input type="hidden" name="first_name" value="{{ $candidate->first_name }}">
                    <input type="hidden" name="last_name" value="{{ $candidate->last_name }}">
                    <input type="hidden" name="phone" value="{{ $candidate->phone }}">

                    <div class="relative w-32 h-32 rounded-full border-2 border-brand-200 overflow-hidden mx-auto shadow-sm">
                        @if($candidate->photograph)
                            <img src="{{ asset('storage/' . $candidate->photograph) }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-brand-50 flex items-center justify-center font-bold text-3xl text-brand-700 uppercase">
                                {{ $candidate->initials }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-2 text-center text-xs text-gray-400">PNG, JPG, JPEG up to 2MB</div>

                    <div>
                        <label class="inline-flex justify-center px-4 py-2 border border-gray-300 text-xs font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm cursor-pointer w-full">
                            Choose Photo
                            <input type="file" name="photograph" class="sr-only" onchange="this.form.submit()">
                        </label>
                        @error('photograph') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </form>
            </div>

            <!-- Password Change Block -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Change Password</h2>
                
                <form action="{{ route('candidate.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                        @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500">
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
