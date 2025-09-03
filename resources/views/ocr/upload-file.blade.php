<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        input[type="file"]:focus-within + label {
            border-color: #3b82f6; /* blue-500 */
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 font-sans">
    
    @include('ocr._modal')

    <header class="bg-gray-800/80 backdrop-blur-sm border-b border-gray-700 sticky top-0 z-10">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <span class="text-lg font-bold text-white">OCR System</span>
                </div>
                <nav class="flex items-center gap-4">
                    <a href="{{ route('ocr.form') }}" class="text-sm font-semibold text-white bg-gray-700 px-3 py-2 rounded-md">Upload</a>
                    <a href="{{ route('search.form') }}" class="text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-colors">Search</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex items-center justify-center min-h-[calc(100vh-64px)]">
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl p-8 max-w-lg w-full m-4">
            
            <div class="text-center">
                <h1 class="text-2xl font-bold text-white">Upload Your Document</h1>
                <p class="text-gray-400 mt-2">Let our AI extract the text from your image or PDF file.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-900/50 text-red-300 border border-red-700 rounded-lg p-4 my-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('ocr.process') }}" method="POST" enctype="multipart/form-data" class="mt-8">
                @csrf
                <div>
                    <input type="file" id="file-upload" name="file" class="hidden" required>
                    <label for="file-upload" class="flex flex-col items-center justify-center w-full h-56 border-2 border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-700/50 hover:bg-gray-700 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-400"><span class="font-semibold text-blue-400">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, or PDF (MAX. 10MB)</p>
                        </div>
                    </label>
                </div>
    
                <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Extract Text
                </button>
            </form>
        </div>
    </main>

</body>
</html>