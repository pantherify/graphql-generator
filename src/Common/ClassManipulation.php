<?php


namespace Pantherify\GraphQLGenerator\src\Common;


use Illuminate\Support\Str;
use ReflectionClass;

class ClassManipulation
{
    /**
     * @param $namespace
     * @return array
     */
    public static function getClassesByNamespace($namespace): array
    {
        $composer = require base_path('/vendor/autoload.php');
        $classes = array_keys($composer->getClassMap());

        $namespace = Str::start(strtoupper($namespace), '\\');

        return array_filter($classes, static function ($class) use ($namespace) {
            $className = Str::start(strtoupper($class), '\\');

            return
                0 === strpos($className, $namespace) &&
                false === stripos($className, 'Abstract') &&
                false === stripos($className, 'Interface');
        });
    }

    /**
     * @param ReflectionClass $class
     * @return bool
     */
    public static function isAllOk(\ReflectionClass $class): bool
    {
        $isSubClass = $class->isSubclassOf('Illuminate\Database\Eloquent\Model');
        $isAbstract = $class->isInstantiable();

        return
            $isSubClass &&
            $isAbstract;
    }


    /**
     * @param ReflectionClass[] $classes
     * @return bool
     */
    private static function hasTrait(array $classes): bool
    {

        foreach ($classes as $class){
            if ($class->getShortName() == "GraphQLModel") return true;
        }
        return false;
    }

}
