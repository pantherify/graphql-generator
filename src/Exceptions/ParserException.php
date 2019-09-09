<?php


namespace Pantherify\GraphQLGenerator\src\Exceptions;


use Doctrine\DBAL\DBALException;

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

    public static function columnTypeMapping(String $columnName, String $columnType)
    {
        return new self("Unmapped Type $columnType at the Column $columnName", 101);
    }

    public static function getRelationError(String $class, \Exception $exception)
    {
        return new self("Error Getting Relations from $class. \n $exception->message", 102, $exception);
    }
}
