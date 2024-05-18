<!-- resources/views/table-item.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Table Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container{
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card {
            display: flex;
            width: 900px;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .content {
            /* display: flex; */
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
            padding: 20px;
        }
        .date{
            float:right;
            padding-right: 10px;
        }
        pre{
            white-space: pre-wrap;      /* Preserve whitespace and new lines */
            word-wrap: break-word;      /* Ensure long words break and wrap to the next line */
            overflow-wrap: break-word;  /* Ensure long words break and wrap to the next line */
            width: 100%;                /* Ensure the <pre> tag takes the full width of its container */
            overflow-x: auto;           /* Add horizontal scrolling if necessary */
            box-sizing: border-box;     /* Include padding and border in the element's total width and height */
            padding: 10px;              /* Add some padding for better readability */
            background-color: #f8f9fa;  /* Optional: Add a background color for better readability */
            border: 1px solid #dee2e6;  /* Optional: Add a border for better readability */
            border-radius: 5px;         /* Optional: Add rounded corners for better aesthetics */
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
                            <h3>{{$record->file_name }}</h3>
                            <span class='date' >{{$record->created_at}}</span>
                        </div>
                        @if (isset($record->transcription['source']))
                            <audio controls preload="none">
                                <source src="{{$record->transcription['source']}}" type="audio/wav">
                            </audio>
                        @endif
                        @if ($record->transcribedPerPerson)
                        <div class='transcription_html'>
                            <pre>
                                {!! $record->transcribedPerPerson["transcription_html"] !!}
                            </pre>
                        </div>

                        @endif
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="content">
                    <div class="content-info">
                        <h3>Teksta analīze</h3>
                        @if ($record->transcribedPerPerson)
                            <div class='transcription_html'>
                                <pre>
                                    {!! $record->transcribedPerPerson["analysed"] !!}
                                </pre>
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
