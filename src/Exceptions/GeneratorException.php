<?php


namespace Pantherify\GraphQLGenerator\src\Exceptions;


/**
 * Class GeneratorException
 * @package Pantherify\GraphQLGenerator\src\Exceptions
 */
class GeneratorException extends \Exception
{
    public static function FlowError(\Exception $exception)
    {
        return new self($exception->message, 300, $exception);
    }
}
