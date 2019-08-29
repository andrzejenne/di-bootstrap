<?php


namespace BigBIT\DIBootstrap\Exceptions;

use Throwable;

/**
 * Class ClassNotFoundException
 * @package BigBIT\DIBootstrap\Exceptions
 */
class ClassNotFoundException extends \Exception
{
    /**
     * ClassNotFoundException constructor.
     * @param string $className
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($className = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Class $className not found", $code, $previous);
    }

}
