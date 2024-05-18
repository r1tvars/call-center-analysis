@if($card['status'] == 'Succeeded')
<div class="card" id="{{ $card['id'] }}" status="succeeded">
    <div class="content">
        <div class="content-info">
            <h3>{{ $card['file_name'] }}</h3>
            <p>batch_id: {{ $card['batch_id'] }}</p>
            <span class="status succeeded">Succeeded</span>
        </div>
        <div class="transcription-info">
            @if (isset($card->transcription['combinedRecognizedPhrases']))
                @foreach ($card->transcription['combinedRecognizedPhrases'] as $phrase)
                    <p>{{ $phrase['display'] }}</p>
                @endforeach
            @endif
        </div>
        <button onclick="window.location='/call-record/' + {{$card['id']}};">Atvērt</button>
    </div>
</div>
@elseif($card['status'] == 'Running')
<div class="card" id="{{ $card['id'] }}" status="in-processing">
    <div class="content">
        <div class="content-info">
            <h3>{{ $card['file_name'] }}</h3>
            <p>batch_id: {{ $card['batch_id'] }}</p>
            <span class="status running">Running</span>
        </div>
        <div class="loader"></div>
    </div>
</div>
@else
<div class="card" id="{{ $card['id'] }}" status="failed">
    <div class="content">
        <div class="content-info">
            <h3>{{ $card['file_name'] }}</h3>
            <p>batch_id: {{ $card['batch_id'] }}</p>
            <span class="status failed">{{ $card['status'] }}</span>
        </div>
        <div class="loader"></div>
    </div>
</div>
@endif
