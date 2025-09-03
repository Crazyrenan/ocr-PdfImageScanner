<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Documents</title>
    <!-- Tailwind CSS CDN -->
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
                    <a href="{{ route('search.form') }}" class="text-sm font-semibold text-white bg-gray-700 px-3 py-2 rounded-md">Search</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto max-w-4xl px-4 py-12">
        
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-white tracking-tight">Search Your Document Library</h1>
            <p class="mt-3 text-lg text-gray-400">Find any document by searching for its content.</p>
        </div>

        <form action="{{ route('search.results') }}" method="GET" class="flex gap-2 max-w-2xl mx-auto">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="search" name="query" placeholder="Enter keywords, phrases, or sentences..." value="{{ $query ?? '' }}" required
                       class="block w-full p-4 pl-12 text-sm text-gray-100 border border-gray-700 rounded-lg bg-gray-800 placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-800 font-medium rounded-lg text-sm px-6 py-4 transition">Search</button>
        </form>

            @if(isset($documents))
            <div class="mt-16">
                
                @if(isset($query))
                    <h2 class="text-2xl font-semibold text-white mb-6">
                        Results for <span class="text-blue-400">"{{ $query }}"</span>
                    </h2>
                @else
                    <h2 class="text-2xl font-semibold text-white mb-6">
                        All Uploaded Documents
                    </h2>
                @endif

                @if($documents->isEmpty())
                    <div class="text-center py-16 px-8 bg-gray-800/50 border border-dashed border-gray-700 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-white">No documents found</h3>
                        <p class="mt-2 text-sm text-gray-400">
                            @if(isset($query))
                                We couldn't find any documents matching your search. Try different keywords.
                            @else
                                You haven't uploaded any documents yet. <a href="{{ route('ocr.form') }}" class="text-blue-400 hover:underline">Upload one now</a>.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="space-y-5">
                       @foreach($documents as $doc)
                            @php
                                $isPdf = Str::endsWith(strtolower($doc->stored_path), '.pdf');
                                $viewRoute = $isPdf ? route('document.viewer', ['id' => $doc->id]) : route('image.viewer', ['id' => $doc->id]);
                                
                                // This is the logic to choose the correct thumbnail
                                $thumbnailUrl = $isPdf && $doc->thumbnail_path ? $doc->thumbnail_path : $doc->stored_path;
                            @endphp

                            <a href="{{ $viewRoute }}" class="block p-6 bg-gray-800 border border-gray-700 rounded-lg shadow hover:bg-gray-700/50 hover:border-blue-600 transition-all duration-200">
                                <div class="flex items-start gap-4">
                                    
                                    <img src="{{ asset('storage/' . $thumbnailUrl) }}" alt="Thumbnail" class="w-24 h-auto rounded-md border border-gray-600 shrink-0">

                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-6 h-6 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <h3 class="text-lg font-semibold text-blue-400 truncate">{{ $doc->original_filename }}</h3>
                                        </div>
                                        <p class="mt-3 text-sm text-gray-400 leading-relaxed">
                                            ...{{ Str::limit(preg_replace('/\s+/', ' ', $doc->extracted_text), 350) }}...
                                        </p>
                                        <div class="mt-4 text-xs text-gray-500">
                                            <span>Uploaded on: {{ $doc->created_at->toFormattedDateString() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </main>
</body>
</html>
