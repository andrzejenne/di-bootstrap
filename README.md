# di-bootstrap
App DI Container bootstrapping with dependencies for PHP7.4

## Example
```php
use BigBIT\DIBootstrap\Bootstrap;

Bootstrap::useVendorPath(dirname(__DIR__) . DIRECTORY_SEPARATOR . "vendor");
Bootstrap::useContainerImplementation(BigBIT\SmartDI\SmartContainer::class);

$bindings = [
    SomeInterface::class => function(ContainerInterface $container) {
        return new SomeImplementation($container); 
    }
];

$container = Bootstrap::getContainer();

$app = new App($container);

$app->run();
```
