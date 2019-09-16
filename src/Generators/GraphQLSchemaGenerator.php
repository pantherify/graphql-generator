<?php


namespace Pantherify\GraphQLGenerator\src\Generators;


use Generator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Pantherify\GraphQLGenerator\src\Models\SchemaNode;

class GraphQLSchemaGenerator
{


    /**
     * @param SchemaNode[] $graphTypes
     * @param array $opt
     * @return Generator
     */
    public static function createModelFiles(array $graphTypes, array $opt): Generator
    {
        if ($opt['delete'])
            GraphQLGenerator::delete_in_path("models");
        else
            GraphQLGenerator::create_base_path("models");

        foreach ($graphTypes as $type) {
            $output = View::make('graphql-gen::model', array('model' => $type))->render();
            $name = strtolower($type['name']);


            File::put(GraphQLGenerator::base_path("models") . GraphQLGenerator::schema_prefix() . "$name-schema.graphql", $output);
            yield "GraphQL Schema Generated : $name";
        }

    }


    /**
     * @param array $graphTypes
     * @param array $opt
     * @return Generator
     */
    public static function createQueryFiles(array $graphTypes, array $opt): Generator
    {
        $view_suffix = $opt['pagination'] ? '' : '-nopagination';

        if ($opt['delete'])
            GraphQLGenerator::delete_in_path("queries");
        else
            GraphQLGenerator::create_base_path("queries");


        foreach ($graphTypes as $type) {
            $output = View::make('graphql-gen::query',
                [
                    'model' => $type,
                    'plural' => Str::plural($type['name']),
                    'single' => Str::singular($type['name'])
                ])->render();
            $name = strtolower($type['name']);

            File::put(GraphQLGenerator::base_path('queries') . GraphQLGenerator::schema_prefix() . "${name}-query${view_suffix}.graphql", $output);
            yield "GraphQL Query Generated : ${name}";

        }
    }

    public static function createMutationFiles(array $graphTypes, array $opt): Generator
    {

        if ($opt['delete'])
            GraphQLGenerator::delete_in_path("mutations");
        else
            GraphQLGenerator::create_base_path("mutations");

        foreach ($graphTypes as $type) {
            $name = strtolower($type['name']);

            // Generate Create Files
            self::generateFile("mutations/create", "mutation-create", $name, $type);
            yield "GraphQL Mutation Generated ##CREATE## : ${name}";

            // Generate Update Files
            self::generateFile("mutations/update", "mutation-update", $name, $type);
            yield "GraphQL Mutation Generated ##UPDATE## : ${name}";

            // Generate Delete Files
            self::generateFile("mutations/delete", "mutation-delete", $name, $type);
            yield "GraphQL Mutation Generated ##DELETE## : ${name}";

        }

    }

    private static function generateFile(String $basePath, String $template, String $name, $type)
    {
        GraphQLGenerator::create_base_path($basePath);
        $output = View::make("graphql-gen::${template}",
            [
                'model' => $type,
                'plural' => Str::plural($type['name']),
                'single' => Str::singular($type['name'])
            ]
        )->render();

        File::put(GraphQLGenerator::base_path($basePath) . GraphQLGenerator::schema_prefix() . "${name}-${template}.graphql", $output);
    }

}

