<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    /**
     * Display list of uploaded documents and options to upload new ones.
     */
    public function index(): View
    {
        $candidate = Auth::guard('candidate')->user();

        // Get documents attached to the candidate directly
        $personalDocs = $candidate->documents()->orderByDesc('created_at')->get();

        // Get documents attached to candidate's applications
        $applicationIds = $candidate->applications()->pluck('id');
        $applicationDocs = Document::where('documentable_type', \App\Models\Application::class)
            ->whereIn('documentable_id', $applicationIds)
            ->orderByDesc('created_at')
            ->get();

        return view('portal.documents.index', compact('personalDocs', 'applicationDocs'));
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request): RedirectResponse
    {
        $candidate = Auth::guard('candidate')->user();

        // Configure validation matching recruitment configs
        $maxSize = config('recruitment.application.max_file_size', 10240); // default 10MB
        $allowedExtensions = implode(',', config('recruitment.application.allowed_extensions', ['pdf', 'doc', 'docx']));
        // Extend slightly to support images for ID proof and certificates
        $allowedMimes = $allowedExtensions . ',jpg,jpeg,png';

        $request->validate([
            'document' => ['required', 'file', 'mimes:' . $allowedMimes, 'max:' . $maxSize],
            'collection' => ['required', 'string', 'in:resume,cover_letter,certificates,identity,portfolio'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('document');
        $collection = $request->collection;

        try {
            // Determine storage directory path matching standard candidates layout
            $folder = 'candidates/' . $candidate->id . '/portal_documents/' . $collection;
            $path = $file->store($folder, 'private');

            // Create polymorphic document entry
            $document = $candidate->documents()->create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'disk' => 'private',
                'collection' => $collection,
                'description' => $request->description,
                'uploaded_by' => null, // null denotes uploaded by candidate
            ]);

            // Update candidate model fields for direct Resume and Cover Letter links
            if ($collection === 'resume') {
                $candidate->update([
                    'resume_path' => $path,
                    'resume_original_name' => $file->getClientOriginalName(),
                ]);
            } elseif ($collection === 'cover_letter') {
                $candidate->update([
                    'cover_letter_path' => $path,
                ]);
            }

            Log::info('Candidate uploaded document successfully', [
                'candidate_id' => $candidate->id,
                'document_id' => $document->id,
                'collection' => $collection
            ]);

            return back()->with('success', 'Document uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to store candidate uploaded document', [
                'candidate_id' => $candidate->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to upload document. Please try again.');
        }
    }

    /**
     * Delete an uploaded document.
     */
    public function destroy(Document $document): RedirectResponse
    {
        $candidate = Auth::guard('candidate')->user();

        // Security ownership check via CandidatePolicy check mapping
        if ($document->documentable_type !== \App\Models\Candidate::class || (int) $document->documentable_id !== (int) $candidate->id) {
            abort(403, 'Unauthorized operation.');
        }

        // Only allow deleting candidate-uploaded documents (where uploaded_by is null)
        if ($document->uploaded_by !== null) {
            abort(403, 'You cannot delete documents uploaded by recruitment staff.');
        }

        try {
            // Delete file from storage
            if (Storage::disk($document->disk)->exists($document->file_path)) {
                Storage::disk($document->disk)->delete($document->file_path);
            }

            $collection = $document->collection;
            $document->delete();

            // Clear Candidate model direct fields if needed
            if ($collection === 'resume') {
                $candidate->update([
                    'resume_path' => null,
                    'resume_original_name' => null,
                ]);
            } elseif ($collection === 'cover_letter') {
                $candidate->update([
                    'cover_letter_path' => null,
                ]);
            }

            Log::info('Candidate deleted document successfully', [
                'candidate_id' => $candidate->id,
                'document_id' => $document->id
            ]);

            return back()->with('success', 'Document deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete candidate document', [
                'candidate_id' => $candidate->id,
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to delete document. Please try again.');
        }
    }
}
