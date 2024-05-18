<div class="card-container">
<div class="button-wrapper">
    <button onclick="window.location='/'">Sākums</button>
</div>
    @foreach($cards as $card)
        @include('card',['card'=>$card])
    @endforeach
</div>