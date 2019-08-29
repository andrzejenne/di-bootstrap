<?php


namespace BigBIT\DIBootstrap\Exceptions;


use Throwable;

/**
 * Class InvalidContainerImplementationException
 * @package BigBIT\DIBootstrap\Exceptions
 */
class InvalidContainerImplementationException extends \Exception
{
    /**
     * InvalidContainerImplementationException constructor.
     * @param $implementation
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($implementation, $code = 0, Throwable $previous = null)
    {
        parent::__construct(get_class($implementation) . " does not implements Psr\Container\ContainerInterface", $code,
            $previous);
    }

}
