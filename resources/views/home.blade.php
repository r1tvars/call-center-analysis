<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Folder</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto mt-10">
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Upload a Folder of Audio Files</h2>
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            <form id="upload-form" action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="files" class="block text-sm font-medium text-gray-700">Choose a folder</label>
                    <input type="file" id="files" name="files[]" webkitdirectory mozdirectory class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            const filesInput = document.getElementById('files');
            if (filesInput.files.length === 0) {
                e.preventDefault();
                alert('Please select a folder to upload.');
            }
        });
    </script>
</body>
</html>
