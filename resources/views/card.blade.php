@if($card['status'] == 'Succeeded')
<div class="card" id={{$card["id"]}} status='succeeded' >
            <div class="content">
                <div class="title">Card 1</div>
                <p>This is the first card with some text content.</p>
            </div>
</div>
@else
<div class="card" id={{$card["id"]}} status='in-processing'>
            <div class="content">
                apstrādē <div class="loader"></div>
            </div>
</div>
@endif