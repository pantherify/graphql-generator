<?php


namespace Pantherify\GraphQLGenerator\src\Generators;


use Generator;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Pantherify\GraphQLGenerator\src\Common\ClassManipulation;
use Pantherify\GraphQLGenerator\src\Common\PathManipulation;
use Pantherify\GraphQLGenerator\src\Exceptions\GeneratorException;
use Pantherify\GraphQLGenerator\src\Parsers\EloquentModelParser;

/**
 * Class GraphQLGenerator
 * @package Pantherify\GraphQLGenerator\src\Generators
 */
class GraphQLGenerator
{

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function schema_prefix()
    {
        return config('graphql-generator.schema.prefix');
    }

    /**
     * @param $suffix
     * @return string
     */
    public static function base_path($suffix)
    {
        return base_path(PathManipulation::joinPaths(config('graphql-generator.schema.path'), $suffix)) . "/";
    }

    /**
     * @param $suffix
     */
    public static function create_base_path($suffix)
    {
        if (is_dir(self::base_path($suffix)) == false) {
            mkdir(rtrim(self::base_path($suffix), "/"), 0777, true);
        }
    }

    public static function delete_in_path($suffix)
    {
        File::deleteDirectory(self::base_path($suffix));
        self::create_base_path($suffix);
    }

    /**
     * @throws GeneratorException
     */
    public static function parseModels(): Generator
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

                yield array(
                    'name' => $schemaName,
                    'namespace' => $reflection_class->getName(),
                    'attributes' => $properties,
                    'relations' => $relations
                );

            } catch (\Exception $e) {
                throw GeneratorException::FlowError($e);
            }

        }

    }
}
