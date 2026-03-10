<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['lead', 'contact', 'deal', 'uploader']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $documents = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
            'name' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'lead_id' => 'nullable|exists:leads,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'deal_id' => 'nullable|exists:deals,id',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        Document::create([
            'name' => $request->name ?: $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'category' => $request->category ?? 'other',
            'lead_id' => $request->lead_id,
            'contact_id' => $request->contact_id,
            'deal_id' => $request->deal_id,
            'uploaded_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Dokumen berhasil diupload!');
    }

    public function download(Document $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Dokumen berhasil dihapus!');
    }
}
