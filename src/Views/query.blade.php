extend type Query {

{{strtolower($plural)}}(orderBy : [OrderByClause!] @orderBy) : [{{$model['name']}}!] @paginate(type: "paginator" model: "{{str_replace("\\","\\\\",$model['namespace'])}}")
{{strtolower($single)}}(id : ID @eq) : {{$model['name']}} @find(model: "{{str_replace("\\","\\\\",$model['namespace'])}}")

}
