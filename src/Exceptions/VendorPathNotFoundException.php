<?php


namespace BigBIT\DIBootstrap\Exceptions;


use Throwable;

/**
 * Class VendorPathNotFoundException
 * @package BigBIT\DIBootstrap\Exceptions
 */
class VendorPathNotFoundException extends \Exception
{
    /**
     * VendorPathNotFoundException constructor.
     * @param string $originPath
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($originPath, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Vendor dir not found from $originPath", $code, $previous);
    }

}