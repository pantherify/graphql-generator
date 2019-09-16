extend type Mutation {

update{{$model['name']}}(
@foreach($model['mutations']['create'] as $attribute)
    {{$attribute['name']}} : {{$attribute['type']}}
@endforeach()

): {{$model['name']}}! @update(model: "{{str_replace("\\","\\\\",$model['namespace'])}}")

}
