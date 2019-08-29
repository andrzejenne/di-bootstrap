<?php

namespace BigBIT\DIBootstrap;

use BigBIT\DIBootstrap\Exceptions\ClassNotFoundException;
use BigBIT\DIBootstrap\Exceptions\InvalidContainerImplementationException;
use BigBIT\DIBootstrap\Exceptions\PathNotFoundException;
use Psr\Container\ContainerInterface;

/**
 * Class Bootstrap
 * @package BigBIT\DIBootstrap
 */
class Bootstrap
{
    /** @var ContainerInterface */
    protected static $container;

    /** @var string */
    private static $autoloadPath = __DIR__ . '../../vendor/autoload.php';

    /** @var string */
    private static $containerClass = 'BigBIT\\SmartDI\\SmartContainer';

    /**
     * @param $vendorPath
     */
    public static function useVendorPath(string $vendorPath)
    {
        static::$autoloadPath = $vendorPath . DIRECTORY_SEPARATOR . 'autoload.php';
    }

    /**
     * @param string $containerClass
     */
    public static function useContainerImplementation(string $containerClass)
    {
        static::$containerClass = $containerClass;
    }

    /**
     * @param array $bindings
     * @return ContainerInterface
     * @throws ClassNotFoundException
     * @throws InvalidContainerImplementationException
     * @throws PathNotFoundException
     */
    public static function getContainer(array $bindings = [])
    {
        if (null === static::$container) {
            static::boot($bindings);
        }

        return static::$container;
    }

    /**
     * @param array $bindings
     * @throws ClassNotFoundException
     * @throws PathNotFoundException
     * @throws InvalidContainerImplementationException
     */
    protected static function boot(array $bindings)
    {
        if (!file_exists(static::$autoloadPath)) {
            throw new PathNotFoundException(static::$autoloadPath);
        }

        require(static::$autoloadPath);

        if (!class_exists(static::$containerClass)) {
            throw new ClassNotFoundException(static::$containerClass);
        }

        static::$container = new static::$containerClass();

        if (!static::$container instanceof ContainerInterface) {
            throw new InvalidContainerImplementationException(static::$container);
        }

        $bindings = array_merge(static::getDefaultBindings(), $bindings);

        foreach ($bindings as $key => $value) {
            static::$container[$key] = $value;
        }

        static::$container[ContainerInterface::class] = static::$container;
    }

    /**
     * @return array
     */
    private static function getDefaultBindings()
    {
        return [
        ];
    }

    /**
     * @return string
     */
    final static protected function getAutoloadPath()
    {
        return static::$autoloadPath;
    }
}
