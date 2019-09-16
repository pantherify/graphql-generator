extend type Mutation {

create{{$model['name']}}(
@foreach($model['mutations']['create'] as $attribute)
    {{$attribute['name']}} : {{$attribute['type']}}
@endforeach()

): {{$model['name']}}! @create(model: "{{str_replace("\\","\\\\",$model['namespace'])}}")

}
