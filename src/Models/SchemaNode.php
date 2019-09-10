<?php


namespace Pantherify\GraphQLGenerator\src\Models;

/**
 * Class SchemaNode
 * @package Pantherify\GraphQLGenerator\src\Models
 */
abstract class SchemaNode
{
    public $name;
    public $attributes;
    public $relations;
}
