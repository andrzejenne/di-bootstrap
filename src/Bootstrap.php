<?php

namespace BigBIT\DIBootstrap;

use BigBIT\DIBootstrap\Exceptions\CannotGetContainerException;
use BigBIT\DIBootstrap\Exceptions\ClassNotFoundException;
use BigBIT\DIBootstrap\Exceptions\InvalidContainerImplementationException;
use BigBIT\DIBootstrap\Exceptions\PathNotFoundException;
use BigBIT\DIBootstrap\Exceptions\VendorPathNotFoundException;
use Psr\Container\ContainerInterface;

/**
 * Class Bootstrap
 * @package BigBIT\DIBootstrap
 */
class Bootstrap
{
    /** @var ContainerInterface */
    protected static ?ContainerInterface $container = null;

    /** @var string */
    private static string $autoloadPath = __DIR__ . '../../vendor/autoload.php';

    /** @var string */
    private static string $containerClass = 'BigBIT\\SmartDI\\SmartContainer';

    /**
     * @param string $sourcePath
     * @param string $vendorDir
     * @throws VendorPathNotFoundException
     */
    public static function detectVendorPath(string $sourcePath = __DIR__, string $vendorDir = 'vendor')
    {
        $path = $sourcePath;

        while (
            !file_exists(
                ($detectingVendorPath = static::getAutoloadPathIn($path . DIRECTORY_SEPARATOR . $vendorDir))
            )
        ) {
            $currentPath = dirname($path);
            if ($currentPath === $path) {
                throw new VendorPathNotFoundException($sourcePath);
            }

            $path = $currentPath;
        }

        static::useVendorPath($detectingVendorPath);
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
     * @throws CannotGetContainerException
     */
    public static function getContainer(array $bindings = []): ContainerInterface
    {
        if (null === static::$container) {
            static::boot($bindings);
        }
        else {
            if (count($bindings)) {
                static::bootContainer(static::$container, $bindings);
            }
        }

        if (!static::$container instanceof ContainerInterface) {
            throw new CannotGetContainerException();
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
