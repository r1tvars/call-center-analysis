<!DOCTYPE html>
<html>
<head>
    <title>Upload Files to Azure Blob Storage</title>
</head>
<body>
    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="files">Choose audio files:</label>
        <input type="file" id="files" name="files[]" multiple required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
