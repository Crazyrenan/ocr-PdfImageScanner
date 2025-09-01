<!DOCTYPE html>
<html>
<head>
    <title>Search Documents</title>
    </head>
<body>
    <div class="container">
        <h1>Search Uploaded Documents</h1>
        <form action="{{ route('search.results') }}" method="GET">
            <input type="text" name="query" placeholder="Search for text..." value="{{ $query ?? '' }}" required>
            <button type="submit">Search</button>
        </form>

        @if(isset($results))
            <hr>
            <h2>Search Results for "{{ $query }}"</h2>
            @if($results->isEmpty())
                <p>No documents found containing that text.</p>
            @else
                <ul>
                    @foreach($results as $doc)
                        <li>
                            <strong>{{ $doc->original_filename }}</strong>
                            <p>
                                ...{{ Str::limit($doc->extracted_text, 300) }}...
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
    </div>
</body>
</html>