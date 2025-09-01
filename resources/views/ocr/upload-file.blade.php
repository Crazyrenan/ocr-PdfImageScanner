<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Image for OCR</title>
    <style>
        body { font-family: sans-serif; display: grid; place-content: center; min-height: 100vh; }
        .container { max-width: 500px; padding: 2rem; border: 1px solid #ccc; border-radius: 8px; }
        .alert { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload an Image</h1>
        <p>Choose an image file to extract text from it.</p>

        @if ($errors->any())
            <div class="alert">
                <strong>Error:</strong> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('ocr.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="image">Choose Image:</label>
                <input type="file" id="image" name="file" required>
            </div>
            <br>
            <button type="submit">Extract Text</button>
        </form>
    </div>
</body>
</html>