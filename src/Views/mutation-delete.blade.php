extend type Mutation {

delete{{$model['name']}}(
id : ID!

): {{$model['name']}}! @delete(model: "{{str_replace("\\","\\\\",$model['namespace'])}}")

}
