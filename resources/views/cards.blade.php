<div class="card-container">
    @foreach($cards as $card)
        @include('card',['card'=>$card])
    @endforeach
</div>