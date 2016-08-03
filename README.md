# Base Bundle

This bundle aims to be the base for all bundles in your Symfony project.

* Bundle
    * Extension declaration
    * CompilerPass declaration
    * Commands declaration
* Extension
    * Extending BaseExtension
    * Implementing EntitiesOverridableExtension
* Configuration
* Compiler Pass
    * Tag Compiler Pass
    * Mapping Compiler Pass
* Providers
    * EntityManager Provider
    * Repository Provider
* Abstract Event Dispatcher

## Bundle

All bundles in Symfony should start with a PHP class, the Bundle class. This
class should always implement the interface
`Symfony\Component\HttpKernel\Bundle\BundleInterface`, but as you know Symfony
always try to make things easy, you can simply extend the base implementation of
a bundle.

```php
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * My bundle
 */
final class MyBundle extends Bundle
{

}
```

I've personally defended some magic behind some parts of a Framework, but you
should always know what is that magic. Let me explain a little bit your bundle
behavior with this implementation.

### Extension declaration

First of all, your extension will be loaded by magic. What does it mean? Well,
the framework will look for your extension following an standard (the Symfony
one). But what happens if your extension (by error or explicitly) doesn't follow
this standard?

Well, nothing will happen. The framework will still look for a non-existing
class and your desired class will never be instanced. You will spend then some
valuable time finding out where the problem is.

First step to do in your project: avoid this magic and define always your
extension by instancing it in your bundle.

```php
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * My bundle
 */
final class MyBundle extends Bundle
{
    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface|null The container extension
     *
     * @throws \LogicException
     */
    public function getContainerExtension()
    {
        return new MyExtension();
    }
}
```

Important, if your bundle is not using any extension, use this method as well
with a null return. Otherwise, even if you don't have any class inside the
DependencyInjection folder, your framework will look for something.

```php
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * My bundle
 */
final class MyBundle extends Bundle
{
    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface|null The container extension
     *
     * @throws \LogicException
     */
    public function getContainerExtension()
    {
        return null;
    }
}
```

### CompilerPass declaration

One of the most unknown Symfony features is the CompilerPass. If you want to
know a little bit about what are they and how to use them, take a look at the
fantastic cookbook
[How to work with compiler passes in bundles](http://symfony.com/doc/current/cookbook/service_container/compiler_passes.html).

You can instance your compiler passes by using the *build* method inside your
bundle as you can see in this example.

``` php
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * My bundle
 */
final class MyBundle extends Bundle
{
    /**
     * Builds bundle.
     *
     * @param ContainerBuilder $container Container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /**
         * Adds compiler passes.
         */
        $container->addCompilerPass(new MyCompilerPass());
    }
}
```

Let's make it easier. Use the *BaseBundle* and you will be able to use the
*getCompilerPasses* method in order to define all your compiler passes.

``` php
use Mmoreram\BaseBundle\BaseBundle;

/**
 * My bundle
 */
final class MyBundle extends BaseBundle
{
    /**
     * Register compiler passes
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses()
    {
        return [
            new MyCompilerPass(),
        ];
    }
}
```

### Commands declaration

A bundle is also responsible to expose all commands into the main application.
Magic is here as well, so all files ending with *Command* and extending Command
or ContainerAwareCommand inside the main folder Command will be instanced and
loaded each time the bundle is instanced.

Same rationale than the Extension one. You're responsible to know where are your
classes, and the bundle should know it in a very explicit way.

By default, this BaseBundle abstract class removes the Command autoload,
allowing you, in your main Bundle class, to return an array of Command
instances.

``` php
/**
 * Class AbstractBundle.
 */
abstract class BaseBundle extends Bundle
{
    // ...

    /**
     * Register Commands.
     *
     * Disabled as commands are registered as services.
     *
     * @param Application $application An Application instance
     */
    public function registerCommands(Application $application)
    {
    }

    // ...
}
```

I highly recommend you to never use Commands with this kind of magic, as
commands should be, as Controllers and EventListeners, only an entry point to
your domain. You can define your commands as services, injecting there all you
need to make it work.

[How to define commands as services](http://symfony.com/doc/current/cookbook/console/commands_as_services.html)

## Extension

Another pain point each time you need to create a new Bundle. The bundle
Extension is some kind of port between the bundle itself and all the dependency
injection environment. You may be used to seeing files like this.

``` php
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 */
class MyExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        /**
         * Setting all config elements as DI parameters to inject them
         */
        $container->setParameter(
            'my_parameter',
            $config['my_parameter']
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        /**
         * Loading DI definitions
         */
        $loader->load('services.yml');
        $loader->load('commands.yml');
        $loader->load('controllers.yml');
    }
}
```

### Extending BaseExtension

Difficult to remember, right? Well, that should never be a problem anymore. Take
a look at this implementation using the BaseExtension.

``` php
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 */
class MyExtension extends BaseExtension
{
    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *
     * @api
     */
    public function getAlias()
    {
        return 'my_bundle';
    }

    /**
     * Return a new Configuration instance.
     *
     * If object returned by this method is an instance of
     * ConfigurationInterface, extension will use the Configuration to read all
     * bundle config definitions.
     *
     * Also will call getParametrizationValues method to load some config values
     * to internal parameters.
     *
     * @return ConfigurationInterface Configuration file
     */
    protected function getConfigurationInstance()
    {
        return new Configuration();
    }

    /**
     * Get the Config file location.
     *
     * @return string Config file location
     */
    protected function getConfigFilesLocation()
    {
        return __DIR__ . '/../Resources/config';
    }

    /**
     * Config files to load.
     *
     * Each array position can be a simple file name if must be loaded always,
     * or an array, with the filename in the first position, and a boolean in
     * the second one.
     *
     * As a parameter, this method receives all loaded configuration, to allow
     * setting this boolean value from a configuration value.
     *
     * return array(
     *      'file1',
     *      'file2',
     *      ['file3', $config['my_boolean'],
     *      ...
     * );
     *
     * @param array $config Config definitions
     *
     * @return array Config files
     */
    protected function getConfigFiles(array $config)
    {
        return [
            'services',
            'commands',
            'controllers',
        ];
    }

    /**
     * Load Parametrization definition.
     *
     * return array(
     *      'parameter1' => $config['parameter1'],
     *      'parameter2' => $config['parameter2'],
     *      ...
     * );
     *
     * @param array $config Bundles config values
     *
     * @return array Parametrization values
     */
    protected function getParametrizationValues(array $config)
    {
        return [
            'my_parameter' => $config['my_parameter'],
        ];
    }
}
```

It may be larger, but seems to be easier to understand, right?
There are only one thing this class will assume. Your services definitions use
yml format. This is because is much more clear than XML and PHP, and because
it's easier to interpret by humans. As you can see in the *getConfigFiles*
method, you return the name of the file without the extension, being this always
*yml*.

You can modify the container as well before and after the container is loaded by
using these two methods.

``` php
/**
 * Hook after pre-pending configuration.
 *
 * @param array            $config    Configuration
 * @param ContainerBuilder $container Container
 */
protected function preLoad(array $config, ContainerBuilder $container)
{
    // Implement here your bundle logic
}

/**
 * Hook after load the full container.
 *
 * @param array            $config    Configuration
 * @param ContainerBuilder $container Container
 */
protected function postLoad(array $config, ContainerBuilder $container)
{
    // Implement here your bundle logic
}
```

### Implementing EntitiesOverridableExtension

One of the coolest features this bundle can bring to your projects is the
extremely easy way you can use Interfaces in your Doctrine declaration instead
of specific implementations.

To understand a little bit more about this topic, take a look at this Symfony
cookbook [How to define Relationships with abstracts classes and interfaces](http://symfony.com/doc/current/cookbook/doctrine/resolve_target_entity.html).

This bundle allows you to define this relation between used interfaces or
abstract classes and their specific implementation. The only thing you have to
do is make your extension an implementation of the interface
EntitiesOverridableExtension. Let's check an example.

``` php
use Mmoreram\BaseBundle\DependencyInjection\BaseExtension;
use Mmoreram\BaseBundle\DependencyInjection\EntitiesOverridableExtension;

/**
 * This is the class that loads and manages your bundle configuration
 */
class MyExtension extends BaseExtension implements EntitiesOverridableExtension
{
    // ...

    /**
     * Get entities overrides.
     *
     * Result must be an array with:
     * index: Original Interface
     * value: Parameter where class is defined.
     *
     * @return array Overrides definition
     */
    public function getEntitiesOverrides()
    {
        return [
            'My\Interface' => 'My\Entity'
        ];
    }
}
```

## Configuration



## Compiler Pass

This library provides you some abstractions for your compiler passes to cover
some specific use cases. Let's check them all.

### Tag Compiler Pass

Imagine you want to get all service with an specific tag. Then you want to call
another service's method with each one of these found services. This scenario is
so common, and the Symfony documentation refers to it as
[How to Work with Service Tags](http://symfony.com/doc/current/service_container/tags.html).

Symfony allows you to do this, as the documentation says, in a very simple way,
but this is not simple neither clear enough. Too many lines written once and
again doing non-domain-related stuff.

Let's check the TagCompilerPass, an abstract class that will make this task
as easy as implementing just 3 tiny methods.

``` php

```