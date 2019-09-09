<?php


namespace Pantherify\GraphQLGenerator\src\Generators;


use Generator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Pantherify\GraphQLGenerator\src\Models\SchemaNode;

class GraphQLSchemaGenerator
{


    /**
     * @param SchemaNode[] $graphTypes
     * @return Generator
     */
    public static function createModelFiles(array $graphTypes) : Generator
    {
       GraphQLGenerator::create_base_path("models");

        foreach ($graphTypes as $type) {
            $output = View::make('graphql-gen::model', array('model' => $type))->render();
            $name = strtolower($type['name']);


            File::put(GraphQLGenerator::base_path("models") . GraphQLGenerator::schema_prefix() . "$name-schema.graphql", $output);
            yield "GraphQL Schema Generated : $name";
        }

    }


    public static function createQueryFiles(array $graphTypes) : Generator {
        GraphQLGenerator::create_base_path("queries");
    }

}

