<!-- resources/views/table-item.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Table Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-info {
            flex-grow: 1;
        }
        .content-info h3 {
            margin: 0 0 10px;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            color: #fff;
        }
        .status.running {
            background-color: #28a745;
        }

        .back-button{
            margin-left: 1em;
            color: rebeccapurple;
            text-decoration: none;
        }
        .back-button-div{
            margin-left: 10px;
            margin-top: 10px;
        }

        .transcription_html{
            border: solid grey;
            border-width: 1px;
            border-radius: 10px;
            padding: 10px;
            margin-top: 5px;
        }
        .date{
            float:right;
            padding-right: 10px;
        }
        
        .audioplayer{
            margin-top: -80px;
        }

    </style>
</head>
<body>
<div class='back-button-div'>
    <a class="back-button" href="{{ url()->previous() }}" >
        Atpakaļ
    </a>
</div>
    <div class="container">
        
        @if ($record)
            <div class="card">
                <div class="content">
                    <div class="content-info">
                        <div>
                            <span>{{$record->file_name }}</span>
                            <span class='date' >{{$record->created_at}}</span>
                        </div>
                        @if (isset($record->transcription['source']))
                        <video controls="" autoplay="" name="media" draggable="true" class='audioplayer'>
                            <source src={{$record->transcription['source']}} type="audio/x-wav">
                        </video>
                        @endif
                        @if ($record->transcribedPerPerson)
                        <div class='transcription_html'>
                            {!! $record->transcribedPerPerson["transcription_html"] !!}
                        </div>
                            
                        @endif
                    </div>
                </div>
            </div>
        @else
            <p>No item found with the given ID.</p>
        @endif
    </div>
</body>
</html>
