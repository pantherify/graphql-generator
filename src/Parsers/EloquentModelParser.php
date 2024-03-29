<?php


namespace Pantherify\GraphQLGenerator\src\Parsers;


use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Pantherify\GraphQLGenerator\Abstracts\GraphQLModel;
use Pantherify\GraphQLGenerator\src\Exceptions\ParserException;
use ReflectionClass;

/**
 * Class EloquentModelParser
 * @package Pantherify\GraphQLGenerator\src\Parsers
 */
class EloquentModelParser
{
    /**
     * @param Model $model
     * @return array
     * @throws ParserException
     */
    static public function getTableProperties(Model $model): array
    {
        $properties = array();
        $table = $model->getConnection()->getTablePrefix() . $model->getTable();
        $schema = $model->getConnection()->getDoctrineSchemaManager();

        try {
            $databasePlatform = $schema->getDatabasePlatform();
            $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
        } catch (DBALException $e) {
            throw ParserException::doctrineProblem($e);
        }

        $columns = $schema->listTableColumns($table);
        $keyName = $model->getKeyName();


        foreach ($columns as $column) {
            $name = $column->getName();
            $type = EloquentModelParser::mapDbTypeToGraphType($column, $keyName);
            $properties[] = array(
                'name' => $name,
                'type' => $type
            );
        }

        return $properties;
    }


    /**
     * @param Column $column
     * @param String $key
     * @return String
     * @throws ParserException
     */
    private static function mapDbTypeToGraphType(Column $column, String $key): String
    {
        $type = $column->getType()->getName();
        switch ($type) {
            case 'string':
            case 'text':
            case 'time':
            case 'guid':
            case 'jsonb':
                return 'String';
            case 'datetimetz':
            case 'datetime':
                return 'DateTime';
            case 'date':
                return 'Date';
            case 'integer':
            case 'bigint':
            case 'smallint':
                return $column->getName() == $key ? 'ID!' : 'Int';
            case 'boolean':
                return 'Boolean';
            case 'decimal':
            case 'float':
                return 'Float';
            default:
                throw ParserException::columnTypeMapping($column->getName(), $type);
        }
    }


    /**
     * @param ReflectionClass $class
     * @param $name
     * @return array
     * @throws ParserException
     */
    static public function getRelations(ReflectionClass $class, $name)
    {
        $relations = array();
        $model = Application::getInstance()->make($name);


        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class != get_class($model) ||
                !empty($method->getParameters()) ||
                $method->getName() == __FUNCTION__) {
                continue;
            }

            try {
                $return = $method->invoke($model);

                if ($return instanceof Relation) {
                    $relations[$method->getName()] = array(
                        'type' => (new ReflectionClass($return))->getShortName(),
                        'model' => (new ReflectionClass($return->getRelated()))->getShortName(),
                        'rel' => $method->getShortName()
                    );;
                }
            } catch (\Exception $e) {
                throw ParserException::getRelationError($class->getShortName(), $e);
            }
        }
        return $relations;
    }


    /**
     * @param $properties
     * @param $name
     * @return array
     * @throws ParserException
     */
    static public function getMutationConfig($properties, $name)
    {

        /** @var GraphQLModel $instance */
        $instance = Application::getInstance()->make($name);

        return [
            'create' => self::mapProperties($properties, $instance->graph_create),
            'create_auth' => $instance->graph_create_auth,
            'update' => self::mapProperties($properties, $instance->graph_update),
            'update_auth' => $instance->graph_update_auth,
            'delete_auth' => $instance->graph_delete_auth
        ];
    }

    static private function mapProperties($properties, $props)
    {
        $output = [];

        if (count($props) > 0) {
            $output = array_merge(array_filter($properties, function ($p) use ($props) {
                return in_array($p['name'], $props, true);
            }), $output);
        } else {
            $output = $properties;
        }

        return $output;
    }

}
