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
     * @param string $vendorPath
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
        require(static::getAutoloadPath());

        $bindings = array_merge(static::getDefaultBindings(), $bindings);

        static::$container = static::createContainer();

        foreach ($bindings as $key => $value) {
            static::$container[$key] = $value;
        }

        static::$container[ContainerInterface::class] = static::$container;
    }

    /**
     * @return array
     */
    protected static function getDefaultBindings()
    {
        return [
        ];
    }

    /**
     * @return string
     * @throws PathNotFoundException
     */
    final static protected function getAutoloadPath()
    {
        if (!file_exists(static::$autoloadPath)) {
            throw new PathNotFoundException(static::$autoloadPath);
        }

        return static::$autoloadPath;
    }

    /**
     * @return mixed
     * @throws ClassNotFoundException
     * @throws InvalidContainerImplementationException
     */
    final private static function createContainer() {
        if (!class_exists(static::$containerClass)) {
            throw new ClassNotFoundException(static::$containerClass);
        }

        $container = new static::$containerClass();

        if (!$container instanceof ContainerInterface) {
            throw new InvalidContainerImplementationException(static::$container);
        }

        return $container;
    }
}
