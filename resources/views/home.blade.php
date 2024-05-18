<!DOCTYPE html>
<html>
<head>
    <title>Call Center Audio Post Analysis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f7f7f7;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .dropbox {
            border: 2px dashed #aaa;
            border-radius: 10px;
            width: 400px;
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            transition: background-color 0.3s;
            cursor: pointer;
        }
        .dropbox.dragover {
            background-color: #e0e0e0;
        }
        .dropbox p {
            color: #aaa;
        }
        .file-input {
            display: none;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .messages {
            margin-bottom: 20px;
        }
        .messages div {
            color: #d9534f;
        }
        .loading {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        .button-wrapper{
            margin-top: 10px;
            min-height: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="messages">
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
        </div>

        <form id="upload-form" action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="dropbox" id="dropbox">
                <p>Drag & Drop files here or click to upload</p>
                <input type="file" id="files" name="files[]" multiple required class="file-input">
            </div>
            <div class="button-wrapper">
                <button type="submit">Upload</button>
                <div id="loading" class="loading" style="display: none;"></div>
            </div>
        </form>
        <div class="button-wrapper">
                <button onclick="window.location='/uploaded'">Saraksts</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dropbox = document.getElementById('dropbox');
            var fileInput = document.getElementById('files');
            var uploadForm = document.getElementById('upload-form');
            var uploadButton = uploadForm.querySelector('button[type="submit"]');
            var loadingIndicator = document.getElementById('loading');

            dropbox.addEventListener('click', function () {
                fileInput.click();
            });

            dropbox.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropbox.classList.add('dragover');
            });

            dropbox.addEventListener('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropbox.classList.remove('dragover');
            });

            dropbox.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropbox.classList.remove('dragover');

                var files = e.dataTransfer.files;
                fileInput.files = files;

                var event = new Event('change');
                fileInput.dispatchEvent(event);
            });

            fileInput.addEventListener('change', function () {
                var fileNames = [];
                for (var i = 0; i < fileInput.files.length; i++) {
                    fileNames.push(fileInput.files[i].name);
                }
                dropbox.querySelector('p').textContent = fileNames.join(', ');
            });

            uploadForm.addEventListener('submit', function () {
                uploadButton.disabled = true;
                uploadButton.style.display = 'none';
                loadingIndicator.style.display = 'inline-block';
            });
        });
    </script>
</body>
</html>
