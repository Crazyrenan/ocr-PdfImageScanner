<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Viewer</title>
    <!-- PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['Fira Code', 'monospace'],
                    },
                },
            },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Fira+Code&display=swap" rel="stylesheet">
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
        mark { background-color: rgba(59, 130, 246, 0.4); color: #f3f4f6; border-radius: 3px; padding: 2px 0; }
        #pdf-viewer canvas { max-width: 100%; height: auto; }
    </style>
</head>
<body class="bg-gray-900 text-gray-300 font-sans antialiased">

    <!-- Top Navigation Bar -->
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
                    <a href="{{ route('ocr.form') }}" class="text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-colors">Upload</a>
                    <a href="{{ route('search.form') }}" class="text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-colors">Search</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="grid grid-cols-1 md:grid-cols-2 h-[calc(100vh-64px)]">
        
        <div class="pdf-column bg-gray-900 p-6 overflow-y-auto">
            <div id="pdf-viewer" class="flex flex-col items-center gap-6"></div>
        </div>

        <div class="text-column bg-gray-800 p-6 flex flex-col overflow-y-hidden border-l border-gray-700">
            <h2 class="text-xl font-semibold text-white mb-4 shrink-0">Extracted Text</h2>
            
            <div class="relative mb-4 shrink-0">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="textSearchInput" placeholder="Search in this document..." class="bg-gray-700 border border-gray-600 text-gray-200 placeholder-gray-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 transition">
            </div>
            
            <div class="relative h-full overflow-y-auto rounded-md bg-gray-900/50">
                <pre id="textContent" class="p-4 font-mono text-sm text-gray-400 leading-relaxed whitespace-pre-wrap break-words">{{ $document->extracted_text }}</pre>
            </div>
        </div>

    </main>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.worker.min.js';
        const pdfUrl = "{{ asset('storage/' . $document->stored_path) }}";
        const viewer = document.getElementById('pdf-viewer');
        
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                pdf.getPage(pageNum).then(function(page) {
                    const scale = 1.8;
                    const viewport = page.getViewport({ scale: scale });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.className = 'rounded-lg shadow-lg';
                    viewer.appendChild(canvas);
                    page.render({ canvasContext: context, viewport: viewport });
                });
            }
        });
        const textContentElement = document.getElementById('textContent');
        const textSearchInput = document.getElementById('textSearchInput');
        const originalText = textContentElement.innerHTML;

        function performTextSearch() {
            const query = textSearchInput.value;
            textContentElement.innerHTML = originalText;
            if (!query.trim()) return;
            const regex = new RegExp(query, 'gi');
            textContentElement.innerHTML = originalText.replace(regex, (match) => `<mark>${match}</mark>`);
        }
        textSearchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') performTextSearch();
        });
    </script>
</body>
</html>
