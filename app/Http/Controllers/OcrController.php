<?php

namespace App\Http\Controllers;

use App\Models\OcrDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrController extends Controller
{
    /**
     * Shows the upload form.
     */
    public function showUploadForm()
    {
        return view('ocr.upload-file');
    }

    /**
     * Processes the uploaded file (image or PDF) for OCR.
     */
    public function processOcr(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpeg,png,jpg,pdf|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();

        // Save the file to the public disk so we can display it later
        $path = $file->store('uploads', 'public');

        try {
            $response = Http::attach(
                'file', // The key must match the Python script
                file_get_contents($file),
                $originalFilename
            )->post('http://127.0.0.1:5000/ocr');

            // This block handles the response from the Python service
            if ($response->successful()) {
                $text = $response->json()['text'];

                // Save the successful result to the database
                $document = new OcrDocument();
                $document->original_filename = $originalFilename;
                $document->extracted_text = $text;
                $document->save();

                return view('ocr.result', [
                    'text' => $text,
                    'document' => $document,
                    'path' => $path // Pass the path to the view
                ]);
            } else {
                // This 'else' block handles errors from the Python service
                Log::error('OCR Service Error: ' . $response->body());
                return redirect()->back()->withErrors(['error' => 'The OCR service returned an error.']);
            }
        } catch (\Exception $e) {
            // This 'catch' block handles connection errors
            Log::error('Connection to OCR service failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Could not connect to the OCR service. Is it running?']);
        }
    }

    /**
     * Shows the search form.
     */
    public function showSearchForm()
    {
        return view('ocr.search');
    }

    /**
     * Handles the search query and displays results.
     */
    public function handleSearch(Request $request)
    {
        $query = $request->input('query');

        // Search the database for documents where the text contains the query
        $results = OcrDocument::where('extracted_text', 'LIKE', "%{$query}%")->get();

        return view('ocr.search', [
            'results' => $results,
            'query' => $query
        ]);
    }
}