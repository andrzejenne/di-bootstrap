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
     * @param string $path
     * @param string $vendorDir
     */
    public static function detectVendorPath(string $path = __DIR__, string $vendorDir = 'vendor')
    {
        while (
            !file_exists(
                static::getAutoloadPathIn($path . DIRECTORY_SEPARATOR . $vendorDir)
            )
        ) {
            $path = dirname($path);
        }

        static::useVendorPath($path . DIRECTORY_SEPARATOR . $vendorDir);
    }

    /**
     * @param string $vendorPath
     */
    public static function useVendorPath(string $vendorPath)
    {
        self::$autoloadPath = static::getAutoloadPathIn($vendorPath);
    }

    /**
     * @param string $containerClass
     */
    public static function useContainerImplementation(string $containerClass)
    {
        self::$containerClass = $containerClass;
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
        else {
            if (count($bindings)) {
                static::bootContainer(static::$container, $bindings);
            }
        }

        return static::$container;
    }

    /**
     * @param ContainerInterface $container
     * @param array $bindings
     */
    public static function setupContainer(ContainerInterface $container, array $bindings = []) {
        static::$container = $container;

        static::bootContainer($container, $bindings);
    }

    /**
     * @param array $bindings
     * @throws ClassNotFoundException
     * @throws PathNotFoundException
     * @throws InvalidContainerImplementationException
     */
    final protected static function boot(array $bindings)
    {
        require(static::getAutoloadPath());

        static::$container = static::createContainer();

        static::bootContainer(static::$container, $bindings);
    }

    /**
     * @param ContainerInterface $container
     * @param array $bindings
     */
    protected static function bootContainer(ContainerInterface $container, array $bindings) {
        $bindings = array_merge(static::getDefaultBindings(), $bindings);

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
        if (!file_exists(self::$autoloadPath)) {
            throw new PathNotFoundException(self::$autoloadPath);
        }

        return self::$autoloadPath;
    }

    /**
     * @return mixed
     * @throws ClassNotFoundException
     * @throws InvalidContainerImplementationException
     */
    final private static function createContainer() {
        if (!class_exists(self::$containerClass)) {
            throw new ClassNotFoundException(self::$containerClass);
        }

        $container = new static::$containerClass();

        if (!$container instanceof ContainerInterface) {
            throw new InvalidContainerImplementationException(static::$container);
        }

        return $container;
    }

    /**
     * @param string $path
     * @return string
     */
    private static function getAutoloadPathIn(string $path) {
        return $path . DIRECTORY_SEPARATOR . 'autoload.php';
    }
}
