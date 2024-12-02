<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function store(UploadedFile $file, array $attributes)
    {
        $document = Document::create([
            'name' => $attributes['name'],
            'description' => $attributes['description'] ?? null,
            'type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'user_id' => auth()->id(),
            'meeting_id' => $attributes['meeting_id'] ?? null,
            'permissions' => $attributes['permissions'] ?? []
        ]);

        // Store initial version
        $this->storeVersion($document, $file);

        return $document;
    }

    public function storeVersion(Document $document, UploadedFile $file)
    {
        $path = $file->store('documents/' . $document->id, 'documents');

        return DocumentVersion::create([
            'document_id' => $document->id,
            'path' => $path,
            'version' => $document->versions()->count() + 1,
            'size' => $file->getSize(),
            'user_id' => auth()->id(),
            'comment' => request('comment')
        ]);
    }

    public function delete(Document $document)
    {
        // Delete all versions
        foreach ($document->versions as $version) {
            Storage::disk('documents')->delete($version->path);
        }

        $document->delete();
    }

    public function updatePermissions(Document $document, array $permissions)
    {
        return $document->update(['permissions' => $permissions]);
    }

    public function preview(Document $document, $version = null)
    {
        $version = $version ?? $document->latestVersion;
        
        if (!$version) {
            throw new \Exception('No version found');
        }

        // Generate preview based on document type
        switch ($document->type) {
            case 'application/pdf':
                return $this->previewPdf($version);
            case 'image/jpeg':
            case 'image/png':
                return $this->previewImage($version);
            default:
                throw new \Exception('Preview not supported for this file type');
        }
    }

    protected function previewPdf($version)
    {
        // Implementation for PDF preview
        return Storage::disk('documents')->get($version->path);
    }

    protected function previewImage($version)
    {
        // Implementation for image preview with potential resizing
        return Storage::disk('documents')->get($version->path);
    }
}