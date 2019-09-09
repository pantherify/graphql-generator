<?php

namespace Pantherify\GraphQLGenerator\Abstracts;


use Illuminate\Database\Eloquent\Model;

abstract class GraphQLModel extends Model
{
    public $graph_update = [];
    public $graph_create = [];

    public $graph_is_auth = false;
    public $graph_auth_role = [];
}
