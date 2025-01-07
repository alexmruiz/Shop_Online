@props(['cardTitle'=>'', 'cardTools'=>'', 'cardFooter'=>''])
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{$cardTitle}}</h3>
        <div class="card-tools">
            {{$cardTools}}

        </div>
    </div>
    <div class="card-body">
        {{$slot}}
    </div>
    <div class="card-footer">
        <div class="float-right">
            {{$cardFooter}}
        </div>
    </div>
</div>