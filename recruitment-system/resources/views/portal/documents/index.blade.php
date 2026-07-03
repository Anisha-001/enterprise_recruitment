@extends('layouts.portal')

@section('title', 'My Documents')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">Documents Portal</h1>
        <p class="text-sm text-gray-500 mt-1">Upload and manage documents needed for your application and onboarding process.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2-Column: Uploaded Documents List -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Personal Profile Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Profile Documents</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($personalDocs as $doc)
                        <div class="p-6 flex justify-between items-center hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-brand-50 rounded-lg text-brand-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="space-y-0.5">
                                    <h3 class="text-sm font-bold text-gray-900">{{ $doc->original_name }}</h3>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 font-medium">
                                        <span class="bg-brand-100 text-brand-800 px-2 py-0.5 rounded uppercase text-[10px] tracking-wide">{{ \App\Models\Document::COLLECTIONS[$doc->collection] ?? $doc->collection }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $doc->formatted_size }}</span>
                                        <span>&bull;</span>
                                        <span>Uploaded {{ $doc->created_at->format('M d, Y') }}</span>
                                    </div>
                                    @if($doc->description)
                                        <p class="text-xs text-gray-500 italic mt-1">{{ $doc->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                @if($doc->uploaded_by === null)
                                    <!-- Delete Form -->
                                    <form method="POST" action="{{ route('candidate.documents.destroy', $doc->id) }}" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg text-xs font-semibold shadow-sm transition">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 py-10">
                            No profile documents uploaded.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Application Documents uploaded by Staff -->
            @if($applicationDocs->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">Application Documents from Recruitment Team</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($applicationDocs as $doc)
                            <div class="p-6 flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <div class="p-3 bg-gray-50 rounded-lg text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="space-y-0.5">
                                        <h3 class="text-sm font-bold text-gray-900">{{ $doc->original_name }}</h3>
                                        <p class="text-xs text-gray-500">Size: {{ $doc->formatted_size }} &bull; Shared on {{ $doc->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right 1-Column: Upload Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 self-start">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Upload Document</h2>
            <form action="{{ route('candidate.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="collection" class="block text-sm font-medium text-gray-700">Document Type</label>
                    <select id="collection" name="collection" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm rounded-md border">
                        <option value="">Select Category...</option>
                        <option value="resume">Resume / CV</option>
                        <option value="cover_letter">Cover Letter</option>
                        <option value="identity">Identity Proof (ID/Passport)</option>
                        <option value="certificates">Academic Certificates</option>
                        <option value="portfolio">Portfolio</option>
                    </select>
                    @error('collection')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="document" class="block text-sm font-medium text-gray-700">File</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-brand-400 transition cursor-pointer relative">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-semibold text-brand-600 hover:text-brand-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="file-upload" name="document" type="file" required class="sr-only">
                                </label>
                            </div>
                            <p class="text-xs text-gray-400">PDF, DOC, DOCX, PNG, JPG up to 10MB</p>
                            <!-- Show selected filename -->
                            <p id="filename-preview" class="text-xs text-brand-700 font-bold hidden"></p>
                        </div>
                    </div>
                    @error('document')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Notes / Description (Optional)</label>
                    <input type="text" id="description" name="description" placeholder="Brief note about the file..." class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-brand-500 focus:border-brand-500 focus:outline-none">
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                        Upload Document
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const fileUpload = document.getElementById('file-upload');
    const preview = document.getElementById('filename-preview');

    fileUpload.addEventListener('change', (e) => {
        if(e.target.files.length > 0) {
            preview.innerText = 'Selected: ' + e.target.files[0].name;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    });
</script>
@endpush
