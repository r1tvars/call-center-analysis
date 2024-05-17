<div class="card-container">
    @foreach([1,2,3,5,6,7] as $card)
        @include('card',['card'=>'card'])
    @endforeach
</div>