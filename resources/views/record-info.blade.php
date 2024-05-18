<!-- resources/views/table-item.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Table Item</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Ieraksts</h1>
    @if ($record)
        <table>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>id</td>
                <td>{{ $record->id }}</td>
            </tr>
            <tr>
                <td>Faila nosaukums</td>
                <td>{{ $record->file_name }}</td>
            </tr>
            <tr>
                <td>Transkripcija</td>
                <td>{{ $record->transcription }}</td>
            </tr>
            <tr>
                <td>statuss</td>
                <td>{{ $record->status }}</td>
            </tr>
            <tr>
                <td>batch id</td>
                <td>{{ $record->batch_id }}</td>
            </tr>
            <tr>
                <td>Izveidots</td>
                <td>{{ $record->created_at }}</td>
            </tr>
            <tr>
                <td>Atjaunināts</td>
                <td>{{ $record->updated_at }}</td>
            </tr>
        </table>
    @else
        <p>No item found with the given ID.</p>
    @endif
</body>
</html>
