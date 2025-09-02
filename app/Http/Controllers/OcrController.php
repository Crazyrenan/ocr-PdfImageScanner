<?php

namespace App\Http\Controllers;

use App\Models\OcrDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrController extends Controller
{
    public function showUploadForm()
    {
        return view('ocr.upload-file');
    }


    public function processOcr(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $path = $file->store('documents', 'public');

        try {
            $response = Http::attach(
                'file',
                file_get_contents($file),
                $originalFilename
            )->post('http://127.0.0.1:5000/ocr');

            if ($response->successful()) {
                $data = $response->json();

                $document = new OcrDocument();
                $document->original_filename = $originalFilename;
                $document->stored_path = $path;
                $document->extracted_text = $data['text'];
                $document->word_data = json_encode($data['word_data']);
                $document->save();
                
                if ($file->getMimeType() == 'application/pdf') {
                    return redirect()->route('document.viewer', ['id' => $document->id]);
                } else {
                    // If it's an image, go to a simple result page
                    return view('ocr.result-image', [
                        'text' => $data['text'],
                        'path' => $path
                    ]);
                }

            } else {
                // ... error handling
                Log::error('OCR Service Error: ' . $response->body());
                return redirect()->back()->withErrors(['error' => 'The OCR service returned an error.']);
            }
        } catch (\Exception $e) {
            // ... connection error handling
            Log::error('Connection to OCR service failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Could not connect to the OCR service. Is it running?']);
        }
    }
    public function showSearchForm()
    {
        return view('ocr.search');
    }

    public function handleSearch(Request $request)
    {
        $query = $request->input('query');
        $results = OcrDocument::where('extracted_text', 'LIKE', "%{$query}%")->get();

        return view('ocr.search', [
            'results' => $results,
            'query' => $query
        ]);
    }
    // in app/Http/Controllers/OcrController.php
    public function showDocumentViewer($id)
    {
        $document = OcrDocument::findOrFail($id);
        return view('ocr.viewer', ['document' => $document]);
    }
}