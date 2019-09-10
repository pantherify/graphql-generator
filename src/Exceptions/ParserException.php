<?php


namespace Pantherify\GraphQLGenerator\src\Exceptions;


use Doctrine\DBAL\DBALException;

/**
 * Class ParserException
 * @package Pantherify\GraphQLGenerator\src\Exceptions
 */
class ParserException extends \Exception
{

    /**
     * In case the Problem resides in doctrine behavior
     * @param DBALException $DBALException
     * @return ParserException
     */
    public static function doctrineProblem(DBALException $DBALException)
    {
        return new self($DBALException->getMessage(), 100, $DBALException);
    }

    /**
     * @param String $columnName
     * @param String $columnType
     * @return ParserException
     */
    public static function columnTypeMapping(String $columnName, String $columnType)
    {
        return new self("Unmapped Type $columnType at the Column $columnName", 101);
    }

    /**
     * @param String $class
     * @param \Exception $exception
     * @return ParserException
     */
    public static function getRelationError(String $class, \Exception $exception)
    {
        return new self("Error Getting Relations from $class. \n $exception->message", 102, $exception);
    }

    /**
     * @param String $property
     * @param String $class
     * @return ParserException
     */
    public static function propertyDoesNotExist(String $property, String $class)
    {
        return new self("Property '$property' does not Exist in class '$class'.");
    }
}
