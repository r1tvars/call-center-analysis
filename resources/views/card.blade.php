@if($card['status'] == 'Succeeded')
<div class="card" id="{{ $card['id'] }}" status="succeeded">
    <div class="content">
        <div class="content-info">
            <h3>{{ $card['file_name'] }}</h3>
            <p>batch_id: {{ $card['batch_id'] }}</p>
            <span class="status succeeded">Succeeded</span>
        </div>
            {{-- @if (isset($card->transcription['combinedRecognizedPhrases']))
                @foreach ($card->transcription['combinedRecognizedPhrases'] as $phrase)
                    <p>{{ $phrase['display'] }}</p>
                @endforeach
            @endif --}}
        <div class="circles-container">
            <div class="circle green">
                <div class="mask full" style="transform: rotate(0deg);"></div>
                <div class="mask half">
                    <div class="fill" style="transform: rotate(0deg);"></div>
                </div>
                <div class="inside-circle">100%</div>
            </div>
            <div class="circle yellow">
                <div class="mask full" style="transform: rotate(72deg);"></div>
                <div class="mask half">
                    <div class="fill" style="transform: rotate(36deg);"></div>
                </div>
                <div class="inside-circle">50%</div>
            </div>
            <div class="circle red">
                <div class="mask full" style="transform: rotate(288deg);"></div>
                <div class="mask half">
                    <div class="fill" style="transform: rotate(144deg);"></div>
                </div>
                <div class="inside-circle">75%</div>
            </div>
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
