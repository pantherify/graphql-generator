<?php

namespace Pantherify\GraphQLGenerator\Abstracts;


use Illuminate\Database\Eloquent\Model;

/**
 * Class GraphQLModel
 * @package Pantherify\GraphQLGenerator\Abstracts
 */
abstract class GraphQLModel extends Model
{
    public $graph_create = [];
    public $graph_create_auth = true;

    public $graph_update = [];
    public $graph_update_auth = true;

    public $graph_delete_auth = true;
}
