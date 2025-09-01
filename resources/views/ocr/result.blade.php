<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OCR Result</title>
    <style>
        body { font-family: sans-serif; display: grid; place-content: center; min-height: 100vh; text-align: center; }
        .container { max-width: 600px; padding: 2rem; border: 1px solid #ccc; border-radius: 8px; }
        img { max-width: 100%; height: auto; margin-bottom: 1rem; border: 1px solid #ddd; }
        pre { background-color: #f4f4f4; padding: 1rem; white-space: pre-wrap; word-wrap: break-word; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <h1>OCR Result</h1>
        @if(isset($imagePath))
            <h2>Uploaded Image:</h2>
            <img src="{{ asset('storage/' . $imagePath) }}" alt="Uploaded Image for OCR">
        @endif
        <h2>Extracted Text:</h2>
        <pre>{{ $text ?? 'No text was found.' }}</pre>
        <a href="{{ route('ocr.form') }}">Try Another Image</a>
    </div>
</body>
</html>