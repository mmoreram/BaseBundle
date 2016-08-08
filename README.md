# Base Bundle

This bundle aims to be the base for all bundles in your Symfony project.

* [Bundle](#bundle)
    * [Extension declaration](#extension-declaration)
    * [CompilerPass declaration](#compilerpass-declaration)
    * [Commands declaration](#commands-declaration)
* [Extension](#extension)
    * [Extending BaseExtension](#extending-baseextension)
    * [Implementing EntitiesOverridableExtension](#implementing-entitiesoverridableextension)
* Configuration
* [CompilerPass](#compilerpass)
    * [Tag CompilerPass](#tag-compilerpass)
    * [Mapping CompilerPass](#mapping-compilerpass)
* [Provider](#provider)
    * [EntityManager Provider](#entitymanager-provider)
    * [Repository Provider](#repository-provider)
* [EventDispatcher](#eventdispatcher)

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

Maybe the file is larger, and you may notice that there are more lines of code,
but seems to be easier to understand, right? This is what clean code means.
There are only one thing this class will assume. Your services definitions use
*yml* format. This is because is much more clear than XML and PHP, and because
it's easier to interpret by humans. As you can see in the *getConfigFiles*
method, you return the name of the file without the extension, being this always
*yml*.

You can modify the container as well before and after the container is loaded by
using these two methods.

``` php
//...

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

//...
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

### Tag CompilerPass

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
use Mmoreram\BaseBundle\CompilerPass\FeederCompilerPass;

/**
 * Class FeederCompilerPass.
 */
final class FeederCompilerPass extends AbstractTagCompilerPass
{
    /**
     * Get collector service name.
     *
     * @return string Collector service name
     */
    public function getCollectorServiceName()
    {
        return 'my.collector.service';
    }
    /**
     * Get collector method name.
     *
     * @return string Collector method name
     */
    public function getCollectorMethodName()
    {
        return 'addClass';
    }
    /**
     * Get tag name.
     *
     * @return string Tag name
     */
    public function getTagName()
    {
        return 'my.tag';
    }
}
```

In this case, first of all we will check that a service with name
*my.collector.service* exists. If exists, we will look for all services with tag
*my.tag* and we will add them into this collector by using the collector method
*addClass*.

Simple.

### Mapping CompilerPass

Another compiler pass interface this package provides you is the one you should
use in order to add your Doctrine entities definition.

> If you use [SimpleDoctrineMapping](https://github.com/mmoreram/SimpleDoctrineMapping)
> this CompilerPass will reduce the complexity of your mapping definition. Use
> this project in order to take control of your mapping definition.

This provided compiler pass is just an extra layer of simplicity for your entity
mapping definition. Let's take a look on how you can do it.

``` php
use Mmoreram\BaseBundle\CompilerPass\MappingCompilerPass;
use Mmoreram\BaseBundle\CompilerPass\MappingBag;
use Mmoreram\BaseBundle\CompilerPass\MappingBagCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MappingCompilerPass
 */
class MappingCompilerPass extends AbstractMappingCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $mappingBagCollection = new MappingBagCollection();

        $mappingBagCollection->addMappingBag(
            new MappingBag(
                'app',
                'cart',
                'doctrine.orm.default_entity_manager',
                'App\Entity\Cart',
                '@AppBundle/Resources/config/doctrine/Cart.orm.yml',
                'true'
            )
        );

        $this->addEntityMappings(
            $container,
            $mappingBagCollection
        );
    }
}
```

As you can see, for this mapping definition we're not using simple data anymore,
but value objects. It is important to know how this *MappingBag* object works in
order to understand how you can setup this mapping data for each active entity.

``` php

/**
 * Class MappingBag.
 */
class MappingBag
{
    /**
     * MappingBag constructor.
     *
     * @param string      $bundle      Bundle name
     * @param string      $name        Name of the entity
     * @param string      $manager     Name of the manager who will manage it
     * @param string      $class       Entity namespace
     * @param string      $mappingFile Mapping file
     * @param string|bool $enabled     This entity is enabled
     */
    public function __construct(
        string $bundle,
        string $name,
        string $manager,
        string $class,
        string $mappingFile,
        $enabled
    );
}
```

**Why using this compiler pass?** Well, not only because you can perfectly know
how your entities are mapped in your project, but because using this
*addEntityMappings* method you will create as well a service per each entity
repository and entity manager.

For example, in the last piece of code we will be able to use as well these
service in our dependency injection definition.

``` yml
services:

    my_service:
        class: App\MyService
        arguments:
            - ""@app.entity_manager.cart"
            - ""@app.repository.cart"
```

These services are automatically created, and if you change any of the entity
mapping definition, for example, if you use it by passing config parameters
instead of plain values, all definitions will change accordingly after clearing
the cache.

## Provider

If you want to create this aliases of repositories and entity managers for your
entities, even if you're not using this Mapping external library, you can do it
by using these two provider services.

For using them, you should add, first of all, a reference of the *providers.yml*
file in your application configuration.

``` yml
imports:
    - { resource: '../../vendor/mmoreram/base-bundle/Resources/config/providers.yml' }
```

### EntityManager provider

Imagine that you're using Symfony and Doctrine in your project. You have an app,
and for any reason you allowed DoctrineBundle to auto-discover all your
entities by default. If you've created many connections and entity managers in
your project, that example will fit your needs as well.

Let's think what happens in your dependency injection files.

``` yml
services:
    cart_manager:
        class: AppBundle\CartManager
        arguments:
            - "@doctrine.orm.default_entity_manager"
```

Whats happening here? Well your service is now coupled to the entity manager
assigned to manage your entity. If your application has only one single entity
or one service, that should be OK, but what happens if your applications has
many entities and many dependency injection files? What if your entity is no
longer managed by the default entity manager, and now is being managed by
another one called *new_entity_manager*?

Will you change all your *yml* files, one by one, looking for references to the
right entity manager, changing them all? And what happens if a service is using
the same entity manager ofr managing two entities, and one of them is not longer
managed by it?

Think about it.

Well, one of the best options here is changing a little bit the way you think
about the entity managers. Let's assume that each entity should have it's own
entity manager, even if all of them are the same one.

Let's use the same entity example. We have an entity called cart, and is part of
our bundle *AppBundle*. Our *CartManager* service is managing some Cart features
and its entity manager is needed.

First step, creation of a new service pointing our Cart entity manager.

``` yml
services:
    app.entity_manager.cart:
        parent: base.entity_manager_provider
        arguments:
            - "App\Entity\Cart"
```

After that, you will be able to use this new service in your other services.
Let's go back to the last example.

``` yml
services:
    cart_manager:
        class: AppBundle\CartManager
        arguments:
            - "@app.entity_manager.cart"
```

If you're using the default Symfony implementation, with the mapping
auto-discover, the result of both implementations will be exactly the same, but
in the future, if you decide to remove the mapping auto-discovering, or you
split your applications in two different connections with several entity
managers, you will only have to focus on your doctrine configuration. After
that, your services will continue using the right entity manager.

> As you could think, using this strategy means that you should never use the
> default entity manager again, and start using one entity manager per entity.
> So, what if your service is managing two entities at the same time? Easy,
> managing *n* entities means coupling to *n* entity managers, even if they are
> the same one. So please, make sure your services are small and do **only**
> what they have to do.