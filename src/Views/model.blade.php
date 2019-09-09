type {{$model['name']}} {

    @foreach($model['attributes'] as $key => $attribute)
    {{$attribute['name']}} : {{$attribute['type']}}
    @endforeach

    @foreach($model['relations'] as $key => $relation)
    @switch($relation['type'])
    @case('BelongsToMany')
    @case('HasMany')
    {{$relation['rel']}} : [{{$relation['model']}}!]! {{'@'}}{{lcfirst($relation['type'])}}
    @break

    @case('HasOne')
    @case('BelongsTo')
    {{$relation['rel']}} : {{$relation['model']}} {{'@'}}{{lcfirst($relation['type'])}}
    @break


    @default
    {{$relation['rel']}} : {{$relation['model']}} {{$relation['type']}}
    @endswitch

    @endforeach
}
