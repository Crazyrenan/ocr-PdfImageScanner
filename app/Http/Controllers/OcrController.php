<?php

namespace App\Http\Controllers;

use App\Models\OcrDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrController extends Controller
{
    public function processFile(Request $request)
    {
        $request->validate(['file' => 'required|file']);

        $file = $request->file('file');

        try {
            $response = Http::attach(
                'file',
                file_get_contents($file),
                $file->getClientOriginalName()
            )->post(config('services.ocr.url') . '/ocr');

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json(['error' => 'Failed to process file.'], 500);

        } catch (\Exception $e) {
            Log::error('OCR service connection failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not connect to OCR service.'], 500);
        }
    }
    public function showUploadForm()
    {
        return view('ocr.upload-file');
    }

    public function processOcr(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file' => 'required|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ocr.form')
                ->with('status', 'error')
                ->with('message', 'Upload failed. Please choose a valid image or PDF file.');
        }

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $path = $file->store('documents', 'public');

        try {
            $response = Http::attach(
                'file',
                file_get_contents($file),
                $originalFilename
            )->post(config('services.ocr.url') . '/ocr');

            if ($response->successful()) {
                $data = $response->json();

                $document = new OcrDocument();
                $document->original_filename = $originalFilename;
                $document->stored_path = $path;
                $document->extracted_text = $data['text'];
                $document->word_data = json_encode($data['word_data']);
                $document->thumbnail_path = $data['thumbnail_path'];
                $document->save();
            
                $isPdf = $file->getMimeType() == 'application/pdf';
                return redirect()->route('ocr.form')
                    ->with('status', 'success')
                    ->with('message', 'File processed successfully! Redirecting...')
                    ->with('document_id', $document->id) 
                    ->with('is_pdf', $isPdf);
            } else {
                $errorMessage = $response->json()['error'] ?? 'The OCR service returned an error.';
                return redirect()->route('ocr.form')
                    ->with('status', 'error')
                    ->with('message', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Connection to OCR service failed: ' . $e->getMessage());
            return redirect()->route('ocr.form')
                ->with('status', 'error')
                ->with('message', 'Could not connect to the OCR service.');
        }
    }

    //search doank
    public function showSearchForm()
    {
        
        $documents = OcrDocument::latest()->get(); 

        return view('ocr.search', ['documents' => $documents]);
    }

    public function handleSearch(Request $request)
    {
        $query = $request->input('query');

       
        if (empty($query)) {
            return redirect()->route('search.form');
        }
        
        
        $documents = OcrDocument::where('extracted_text', 'LIKE', "%{$query}%")
            ->latest()
            ->get();
        
        return view('ocr.search', [
            'documents' => $documents,
            'query' => $query
        ]);
    }
   
    public function showDocumentViewer($id)
    {
        $document = OcrDocument::findOrFail($id);
        return view('ocr.viewer', ['document' => $document]);
    }
    public function showImageViewer($id)
    {
        $document = OcrDocument::findOrFail($id);

        return view('ocr.result-image', [
            'text' => $document->extracted_text,
            'path' => $document->stored_path
        ]);
    }
}