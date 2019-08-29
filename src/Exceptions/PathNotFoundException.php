<?php


namespace BigBIT\DIBootstrap\Exceptions;

use Throwable;

/**
 * Class PathNotFoundException
 * @package BigBIT\DIBootstrap\Exceptions
 */
class PathNotFoundException extends \Exception
{
    /**
     * PathNotFoundException constructor.
     * @param string $path
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($path = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Path $path not found", $code, $previous);
    }

}
