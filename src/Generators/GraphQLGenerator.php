<?php


namespace Pantherify\GraphQLGenerator\src\Generators;


use Generator;
use Illuminate\Foundation\Application;
use Pantherify\GraphQLGenerator\src\Common\ClassManipulation;
use Pantherify\GraphQLGenerator\src\Common\PathManipulation;
use Pantherify\GraphQLGenerator\src\Exceptions\GeneratorException;
use Pantherify\GraphQLGenerator\src\Parsers\EloquentModelParser;

class GraphQLGenerator
{

    public static function schema_prefix()
    {
        return config('graphql-generator.schema.prefix');
    }

    public static function base_path($suffix)
    {
        return base_path(PathManipulation::joinPaths(config('graphql-generator.schema.path'), $suffix)) . "/";
    }

    public static function create_base_path($suffix)
    {
        if (is_dir(GraphQLGenerator::base_path($suffix)) == false) {
            mkdir(rtrim(GraphQLGenerator::base_path($suffix), "/"), 0777, true);
        }
    }


    /**
     * @throws GeneratorException
     */
    public static function generateModels(): Generator
    {
        $namespace = config('graphql-generator.models.namespace', "App");
        $classes = ClassManipulation::getClassesByNamespace($namespace);

        foreach ($classes as $class) {
            if (!class_exists($class)) continue;

            try {
                $reflection_class = new \ReflectionClass($class);
                if (!ClassManipulation::isAllOk($reflection_class)) continue;

                $model = Application::getInstance()->make($class);

                $schemaName = $reflection_class->getShortName();
                $properties = EloquentModelParser::getTableProperties($model);
                $relations = EloquentModelParser::getRelations($reflection_class, $class);

                EloquentModelParser::getQueries($properties, $class);


                yield array(
                    'name' => $schemaName,
                    'attributes' => $properties,
                    'relations' => $relations
                );

            } catch (\Exception $e) {
                throw GeneratorException::FlowError($e);
            }

        }

    }
}
