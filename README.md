# Base Bundle

[![Build Status](https://travis-ci.org/mmoreram/BaseBundle.png?branch=master)](https://travis-ci.org/mmoreram/BaseBundle)
[![Latest Stable Version](https://poser.pugx.org/mmoreram/base-bundle/v/stable.png)](https://packagist.org/packages/mmoreram/base-bundle)
[![Latest Unstable Version](https://poser.pugx.org/mmoreram/base-bundle/v/unstable.png)](https://packagist.org/packages/mmoreram/base-bundle)

-----

> The minimum requirements of this bundle is **PHP 7.1** and **Symfony 3.2** 
> because the bundle is using features on both versions. If you're not using
> them yet, I encourage you to do it.

This bundle aims to be the base for all bundles in your Symfony project. Know
about these three big blocks.

**Bundles**

* [Bundle extension](#bundle-extension)
    * [Bundle dependencies](#bundle-dependencies)
    * [Extension declaration](#extension-declaration)
    * [Compiler Pass declaration](#compiler-pass-declaration)
    * [Commands declaration](#commands-declaration)
* [Extension](#extension)
    * [Extending BaseExtension](#extending-baseextension)
    * [Implementing EntitiesOverridableExtension](#implementing-entitiesoverridableextension)
* [Configuration](#configuration)
    * [Extension alias](#extension-alias)
    * [Extending BaseConfiguration](#extending-baseconfiguration)
* [Compiler Pass](#compiler-pass)
    * [Tag Compiler Pass](#tag-compiler-pass)
* [Provider](#provider)
    * [ObjectManager Provider](#objectmanager-provider)
    * [ObjectRepository Provider](#objectrepository-provider)

**Functional Tests**

* [Functional Tests](#functional-tests)
    * [BaseKernel](#basekernel)
    * [BaseFunctionalTest](#basefuncionaltest)
    * [Fast testing methods](#fast-testing-methods)
    * [Working with Database](#working-with-database)
    * [Working with Fixtures](#working-with-fixtures)
    * [BaseFixture](#basefixture)

**Entity mapping**

* [Entity Mapping](#entity-mapping)
    * [Private bundles](#private-bundles)
    * [Public bundles](#public-bundles)
    * [Bundles and Components](#bundles-and-components)
    * [Exposing your mapping without BaseBundle](#exposing-your-mapping-without-basebundle)

## Bundle extension

All bundles in Symfony should start with a PHP class, the Bundle class. This
class should always implement the interface
`Symfony\Component\HttpKernel\Bundle\BundleInterface`, but as you know Symfony
always try to make things easy, so you can simply extend the base implementation
of a bundle.

```php
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * My bundle
 */
final class MyBundle extends Bundle
{

}
```

I've personally defended the magic behind some parts of the Framework, but you
should always know what is that magic and discover how affect in your project.
Let me explain a little bit your bundle behavior with this implementation.

### Bundle dependencies

When we talk about dependencies we are used to talking about PHP dependencies.
If we use a file, then this file should be inside our vendor folder, right? That
sounds great, but what about if a bundle needs another bundle to be instanced as
well in our kernel? How Symfony is supposed to handle this need?

Well, the project itself is not providing this feature at this moment, but even
if the theory says that a bundle should never have an external bundle
dependency, the reality is another one, and as far as I know, implementations
cover mostly real problems not nice theories.

Let's check [Symfony Bundle Dependencies](https://github.com/mmoreram/symfony-bundle-dependencies).
By using this *BaseBundle*, your bundle has automatically dependencies (by
default, none).

``` php
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mmoreram\SymfonyBundleDependencies\DependentBundleInterface;

/**
 * Class AbstractBundle.
 */
abstract class BaseBundle extends Bundle implements DependentBundleInterface
{
    //...

    /**
     * Create instance of current bundle, and return dependent bundle namespaces
     *
     * @return array Bundle instances
     */
    public static function getBundleDependencies(KernelInterface $kernel)
    {
        return [];
    }
}
```

If your bundle has dependencies, feel free to overwrite this method in your
class and add them all. Take a look at the main library documentation to learn
a bit more about how to work with dependencies in your Kernel.

### Extension declaration

First of all, your extension will be loaded by magic. What does it mean? Well,
the framework will look for your extension following an standard (the Symfony
one). But what happens if your extension (by error or explicitly) doesn't follow
this standard?

Well, nothing will happen. The framework will still looking for a non-existing
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
        return new MyExtension($this);
    }
}
```

As you can see, your extensions will require the bundle itself as the first and
only construct parameter. Check the configuration chapter to know why.

Even this is the default behavior you can be more explicit and overwrite this 
method to define that your bundle is not using any extension. That will help you
to comprehend a little bit more your bundle requirements.

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

### Compiler Pass declaration

One of the most unknown Symfony features is the Compiler Pass. If you want to
know a little bit about what are they and how to use them, take a look at the
fantastic cookbook
[How to work with Compiler Passes in bundles](http://symfony.com/doc/current/cookbook/service_container/compiler_passes.html).

You can instance your Compiler Passes by using the *build* method inside your
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
         * Adds Compiler Passes.
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
        return 'app';
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

The way your bundle will request and validate some data from the outside (app)
is by using a configuration file. You can check the official
[Configuration Documentation](http://symfony.com/doc/current/components/config/definition.html)
if you want to know a little bit about this amazing feature.

Let's create a new configuration file for our bundle, and let's discover some
nice features this library will provide you by extending the Configuration file.

``` php
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Mmoreram\BaseBundle\DependencyInjection\BaseConfiguration;

/**
 * Class AppConfiguration.
 */
class AppConfiguration extends BaseConfiguration
{
    /**
     * {@inheritdoc}
     */
    protected function setupTree(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('skills')
                    ->prototype('scalar')
                    ->end()
                ->end()
            ->end();
    }
}
```

Ops! What happens here? Lets check it out step by step.

### Extension Alias

First of all, the configuration file will never define it's own name again. The
configuration file should only define what kind of data should request to the
app but not under what namespace.

So, who should define this namespace? The Extension should as is the one that
really extends the dependency injection environment. In other words, even if for
sure this will never be your scenario, you should be able to share a
configuration file between different extensions.

So... how we can do that? If your configuration files extend this one, then, as
long as you want to initialize it, you will have to define it's namespace. Take
a look at this example. This method is part of your Extension file.

``` php
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
    return new Configuration(
        $this->getAlias()
    );
}
```

### Extending BaseConfiguration

By extending the *BaseConfiguration* class you have this alias parameter in the
constructor by default.

``` php
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
    return new BaseConfiguration(
        $this->getAlias()
    );
}
```

Using this class, you don't have to worry anymore about how to create the
validation tree. Just define the validation tree under your extension defined 
alias.

``` php
/**
 * Configure the root node.
 *
 * @param ArrayNodeDefinition $rootNode Root node
 */
protected function setupTree(ArrayNodeDefinition $rootNode)
{
    $rootNode
        ->children()
            ...
}
```

By default, if you don't overwrite this method, no parametrization will be added
under your bundle.

## Compiler Pass

This library provides you some abstractions for your compiler passes to cover
some specific use cases. Let's check them all.

In case you don't know what a Compiler Pass is yet, I encourage you to start with
the documentation with
[How to work with compiler passes in bundles](http://symfony.com/doc/current/service_container/compiler_passes.html).
The sooner you understand the importance of this class, the sooner you will have
better bundles in your projects.

That simple.

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

> The Compiler Pass sorts as well the services before adding them all in the
> collector. To make this happen, you can add the `priority` in your tag line.

## Provider

If you want to create this aliases of repositories and entity managers for your
entities, even if you're not using any Mapping external library, you can do it
by using these two provider services.

For using them, you should add, first of all, a reference of the *providers.yml*
file in your application configuration.

``` yml
imports:
    - { resource: '../../vendor/mmoreram/base-bundle/Resources/config/providers.yml' }
```

If BaseBundle is instanced in your kernel you can use the short bundle mode as
well.

``` yml
imports:
    - { resource: '@BaseBundle/Resources/config/providers.yml' }
```

### ObjectManager Provider

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
        parent: base.abstract_object_manager
        arguments:
            - App\Entity\Cart
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

### ObjectRepository Provider

Same for repositories. What if you want to inject your entity repository in your
services? Well, you can do it by using the same strategy that you did in entity
managers.

``` yml
services:
    app.entity_repository.cart:
        parent: base.abstract_object_repository
        arguments:
            - App\Entity\Cart
```

After that, you'll be able to inject this new service in your domain.

``` yml
services:
    cart_manager:
        class: AppBundle\CartManager
        arguments:
            - "@app.entity_repository.cart"
```

## Functional Tests

Some of the issues many projects have when they want to start testing their
bundles in a functional way is that they don't really know how to handle
with the kernel. The steps to follow are always the same.

* Create a small bundle where to test your features
* Create a kernel that works as a standalone application
* Create a configuration for that kernel

But then some issues come as long as we want to test against several kernels and
different kernel configurations.

How can we solve this?

Well, this is not going to be a problem anymore, at least with this library.
Let's see a functional test and the way you can do it since this moment.

``` php
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;

/**
 * Class TagCompilerPassTest.
 */
final class TagCompilerPassTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return new BaseKernel(
            [
                'Mmoreram\BaseBundle\Tests\Bundle\TestBundle',
            ],
            [
                'services' => [
                    'my.service' => [
                        'class' => 'My\Class',
                        'arguments' => [
                            "a string",
                            "@another.service"
                        ]
                    ]
                ],
                'parameters' => [
                    'locale' => 'es'
                ],
                'framework' => [
                    'form' => true
                ]
            ],
            [
                ['/login', '@MyBundle:User:login', 'user_login'],
                ['/logout', '@MyBundle:User:logout', 'user_logout'],
            ]
        );
    }

    /**
     * Test compiler pass.
     */
    public function testCompilerPass()
    {
        // do your tests
    }
}
```

As you can see, you can do as many things as you need in order to create a
unique scenario. With a simple class (your test) you can define all your app
environment.

Let's see step by step what can you do here

### BaseKernel

This library provides you a special kernel for your tests. This kernel is
testing ready and allow you to customize as much as you need your application
in each scenario. Each testing class will work with a unique kernel
configuration, so all test cases inside this test class will be executed against
this kernel.

This kernel uses the
[Symfony Bundle Dependencies project](http://github.com/mmoreram/symfony-bundle-dependencies)
by default, so make sure you take a look at this project. Using it is not a must
but a great option.

Let's see what do you need to create your own Kernel using the one this library
offers to you.

``` php
new BaseKernel(
    [
        'Mmoreram\BaseBundle\Tests\Bundle\TestBundle',
    ],
    [
        'imports' => [
            ['resource' => '@BaseBundle/Resources/config/providers.yml'],
        ],
        'services' => [
            'my.service' => [
                'class' => 'My\Class',
                'arguments' => [
                    "a string",
                    "@another.service"
                ]
            ]
        ],
        'parameters' => [
            'locale' => 'es'
        ],
        'framework' => [
            'form' => true
        ]
    ],
    [
        ['/login', '@MyBundle:User:login', 'user_login'],
        ['/logout', '@MyBundle:User:logout', 'user_logout'],
        '@MyBundle/Resources/routing.yml',
    ]
);
```

Only three needed parameters for the kernel creation.

* Array of bundle namespaces you need to instance the kernel. If you don't want
to use the Symfony Bundle Dependencies project, make sure you add all of them.
Otherwise, if you use the project, you should only add the bundle/s you want to
test.

* Configuration for the dependency injection component. Use the same format as
you were using *yml* files but in PHP.

* Routes. You can define single routes with an array of three positions. The 
first one is the path, the second one the Controller notation and the last one,
the name of the route. You can define resources with the resource name.

In your configuration definition, and because of mostly all testing cases can be
executed against FrameworkBundle and/or DoctrineBundle, you can preload a simple
configuration per each bundle by adding these lines in your configuration array.

``` php
new BaseKernel(
    [
        'Mmoreram\BaseBundle\Tests\Bundle\TestBundle',
    ],
    [
        'imports' => [
            ['resource' => '@BaseBundle/Resources/config/providers.yml'],
            ['resource' => '@BaseBundle/Resources/test/framework.test.yml'],
            ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
        ],
        'services' => [
            'my.service' => [
                'class' => 'My\Class',
                'arguments' => [
                    "a string",
                    "@another.service"
                ]
            ]
        ],
    ],
    [
        ['/login', '@MyBundle:User:login', 'user_login'],
        ['/logout', '@MyBundle:User:logout', 'user_logout'],
        '@MyBundle/Resources/routing.yml',
    ]
);
```

#### Cache and logs

The question here would be... okay, but where can I find my Kernel cache and
logs? Well, each kernel configuration (bundles, configuration and routing) is
hashed in a unique string. Then, the system creates a folder under the 
`/tmp/base-kernel` folder and creates a unique `kernel-{hash}` folder
inside.

Each time you reuse the same kernel configuration, this previous generated cache
will be used in order to increase the performance of the tests.

To increase *much more* this performance, don't hesitate to create a tmpfs
inside this `/tmp/base-kernel` folder by using this command. 

``` bash
sudo mount -t tmpfs -o size=512M tmpfs /tmp/base-kernel/
```

### BaseFunctionalTest

As soon as you have the definition of how you should instance you kernel, we
should create our first functional test. Let's take a look at how we can do
that.

``` php
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;

/**
 * Class TagCompilerPassTest.
 */
final class TagCompilerPassTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $kernel;
    }

    /**
     * Test compiler pass.
     */
    public function testCompilerPass()
    {
        // do your tests
    }
}
```

In every scenario your kernel will be created and saved locally. You can create
your own kernel or use the *BaseKernel*, in both cases this will work properly,
but take in account that this kernel will be active in the whole scenario.

### Fast testing methods

Functional tests should test only application behaviors, so we should be able to
reduce all this work that is not related to this one.

BaseFunctionalTest has a set of easy-to-use methods for use.

#### ->get() 

if you want to use any container service just call this method (like in 
controllers)

``` php
$this->assetInstanceOf(
    '\MyBundle\My\Service\Namespace',
    $this->get('service_name')
);
```

#### ->has()

if you want to check if a container service exists, call this method. Useful for
service existence testing

``` php
$this->assertTrue(
    $this->get('service_name')
);
```

#### ->getParameter()

if you want to use any container parameter just call this method (like in
controllers)

``` php
$this->assertEqual(
    'en',
    $this->getParameter('locale')
);
```

#### ->getObjectRepository()

find in a fast way the object repository associated to an entity. You can define
your entity namespace in several ways

* MyBundle\Entity\Namespace\User
* MyBundle:User

``` php
$this->assetInstanceOf(
    '\Doctrine\Common\Persistence\ObjectRepository',
    $this->getRepository('MyBundle:User')
);
```

#### ->getObjectManager()

find in a fast way the object manager associated to an entity. You can define
your entity namespace like in *->getObjectRepository()* method

``` php
$this->assetInstanceOf(
    '\Doctrine\Common\Persistence\ObjectManager',
    $this->getManager('MyBundle:User')
);
```

#### ->find()

find one entity instance by its namespace and id. You can define your entity 
namespace like in *->getObjectRepository()* method

``` php
$this->assetInstanceOf(
    '\MyBundle\Entity\User',
    $this->find('MyBundle:User', 1)
);
```

#### ->findOneBy()

find one entity instance complaining the passed criteria. You can define your
entity namespace like in *->getObjectRepository()* method

``` php
$this->assetInstanceOf(
    '\MyBundle\Entity\User',
    $this->findOneBy('MyBundle:User', [
        'name' => 'mmoreram',
    ])
);
```

#### ->findAll()

get all entities given the namespace. You can define your entity namespace like
in *->getObjectRepository()* method

``` php
$this->assertCount(
    10,
    $this->findAll('MyBundle:User')
);
```

#### ->findBy()

get all entities complaining the passed criteria. You can define your entity
namespace like in *->getObjectRepository()* method

``` php
$this->assertCount(
    3,
    $this->findBy('MyBundle:User', [
        'name' => 'mmoreram',
    ])
);
```

#### ->clear()

Clear the entity manager associated to an entity. This means that you force
doctrine to detach the entity type passed. You can define your entity namespace
like in *->getObjectRepository()* method

``` php
$this->clear('MyBundle:User');
```

This is useful when you save entities or changes from already existing entities
and you want to test if the changes have really been applied. This flushes this
cache.

#### ->save()

Save any entity in an easy way. You can save an entity or an array of entities.

``` php
$this->save($user1);
$this->save([
    $user2,
    $user3,
]);
```

The method always persists, even if the entity is already attached to the object
manager.

### Working with Database

Of course, you may need to build the database schema in your tests, and because
most of the cases your database creation are the same, you will be able to apply
these steps just overwriting this method in your test.

``` php
/**
 * Schema must be loaded in all test cases.
 *
 * @return bool Load schema
 */
protected static function loadSchema()
{
    return true;
}
```

If you allow to load the schema, your database will be loaded at the beginning
of your test case and will be dropped after it. By loaded we mean these steps

``` bash
> bin/console doctrine:database:drop --force
> bin/console doctrine:database:create
> bin/console doctrine:schema:create
```

You can debug your console output by overwriding the `debug` protected variable
in your test case.

``` php
/**
 * @var bool
 *
 * Debug mode
 */
protected static $debug = true;
```

### Working with Fixtures

The other need you may have in your functional tests is, after loading the
database, load some fixtures. Because like the kernel, each fixtures
configuration should be unique per each test case, you can define a set of
fixtures in each test case overwriting a method, by default empty.

``` php
/**
 * Load fixtures of these bundles.
 *
 * @return array
 */
protected static function loadFixturePaths() : array
{
    return [
        '@MyBundle',
        '@MyOtherBundle,
    ];
}
```

By default, if you return an array of fixtures, the system will understand that
you want to enable the database schema loading, so you don't need to overwrite
the method `loadSchema()`.

In this method you can add folders where to look for the fixtures, for example
a bundle with short notation, or even a single file. To make sure you treat your
fixtures properly, make sure you use the
[DependentFixtureInterface](https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/DependentFixtureInterface.php)
feature to define each fixture dependencies.

You can as well reset the fixtures in any part of your tests with the method 
`reloadFixtures`. This method will set the database as clean as before starting
with the first current Test Case method.

```php
$this->reloadFixtures();
```

### BaseFixture

As long as you need to create your Fixtures, this library provides you as well
the same container accessors than provided in tests. Just make sure that your
fixtures extend the `BaseFixture` class, and you'll be able to use all these
methods as well.

``` php
/**
 * Class UserData.
 */
class UserData extends BaseFixture
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setName('Joan');
        $this->save($user1);

        $this->find('my_prefix:user');
        $this->getObjectManager('my_prefix:user');

        // ...
    }
}
```

Even if you extend `BaseFixture` you can implement the same interfaces you've
been using until now for dependent fixtures and ordered fixtures.

> To make sure your fixtures are valid even if you decide in the future that
> your entity User is not managed anymore by the default Doctrine entity
> manager, use the ->save() method to persist and flush all entities. With these
> helpers should should never use the manager passed as parameter. If you need
> to get the whole object manager, use the ->getObjectManager() method.

## Entity Mapping

Imagine this scenario.

You have a bundle with a model inside of it. By a model I mean a set of entity
classes and a set of mapping files. You want to provide a simple way of
defining this relation between the mapping files and the entity classes, and at
the same, and by default, create some extra services and configuration layers
to manage this configuration.

By default, Doctrine adds an auto-mapping layer with a not-very-good overwriting
policy, so. let's see how can I disable this auto-mapping layer and start by
using this super easy mapping layer.

### Private bundles

In your private bundles, you may need only a soft layer of your model
definition.

Let's introduce a simple interface called `MappingBagProvider`. If your bundle
has a model, then your bundle has an instance of this class.

``` php
/**
 * Interface MappingBagProvider.
 */
interface MappingBagProvider
{
    /**
     * Get mapping bag collection.
     *
     * @return MappingBagCollection
     */
    public function getMappingBagCollection() : MappingBagCollection;
}
```

As you can see, one simple method and that will be enough. Let's see a simple
implementation of this class for your bundle. For this scenario, imagine that we
have this bundle configuration.

* One entity called User under `MyBundle\Entity` namespace.
* One mapping file under `@MyBundle/Resources/config/doctrine` folder called
  `User.orm.yml`

Our MappingBagProvider should be something like this

``` php
/**
 * Class MappingBagProvider.
 */
class MyBundleMappingBagProvider implements MappingBagProvider
{
    /**
     * Get mapping bag collection.
     *
     * @return MappingBagCollection
     */
    public function getMappingBagCollection() : MappingBagCollection
    {
        return MappingBagCollection::create(
            ['user' => 'User'],
            '@MyBundle',
            'MyBundle\Entity'
        );
    }
}
```

As you can see, this method returns a `MappingBagCollection` instance with some
simple data.

* An associative array of your entities
* A bundle path where to look for the mapping files (you can use the short
  notation here)
* The namespace where to find the entity classes

The second and last step to start working with your entities is the creation of
a compiler pass in your bundle class. If you work with BaseBundle, then make use
of the method defined for that.

``` php
final class MyBundle extends BaseBundle
{
    /**
     * Return a CompilerPass instance array.
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses()
    {
        return [
            new MappingCompilerPass(new MyBundleMappingBagProvider()),
        ];
    }
}
```

and that's it, you model is already built with these amazing features.

* Your entities are mapped with the YAML files inside the Resources path,
  created from the MappingBagCollection construct data. You should follow the
  Symfony standard by placing these mapping files inside the folder
  `@MyBundle/Resources/config/doctrine` with the standard name `User.orm.yml`.
  At the moment, only available for YAML files.
* Per each entity mapped, the library has created two services.
    * `object_manager.{entity_name}` is an alias for the object manager assigned
      to this entity. You can inject it in your services. In that case you could
      use the service `object_manager.user` as an instance of
      `Doctrine\Common\Persistence\ObjectManager`
    * `object_repository.{entity_name}` is an alias for the object repository
      assigned to this entity. You can inject it as well in your services. In
      that case you could the service `object_repository.user` as an instance of
      `Doctrine\Common\Persistence\ObjectRepository`
* Per each entity mapped, you can find as well 4 parameters defined in your
  container, injectable as well in your services
    * `entity.{entity_name}.class` is the entity namespace used for the mapping.
      in that case, `entity.user.class` with a value of `MyBundle\Entity\User`.
    * `entity.{entity_name}.mapping_file` is the path of the mapping file used
      for the mapping of this class. in that case `entity.user.mapping_file`
      with a value of `@MyBundle/Resources/config/doctrine/User.orm.yml`
    * `entity.{entity_name}.manager` is the manager assigned to this entity, by
      default always `default`. In that case `entity.user.manager` with a value
      of `default`.
    * `entity.{entity_name}.enabled` is useful for next chapter, and has whether
      the entity is enabled or not. By default true. In that case 
      `entity.user.enabled` with a value of *true*

Of course, many of these things can be configured by adding more parameters in
our MappingBagProvider implementation.

``` php
/**
 * Class MappingBagProvider.
 */
class MyBundleMappingBagProvider implements MappingBagProvider
{
    /**
     * Get mapping bag collection.
     *
     * @return MappingBagCollection
     */
    public function getMappingBagCollection() : MappingBagCollection
    {
        return MappingBagCollection::create(
            ['user' => 'User'],
            '@MyBundle',
            'MyBundle\Entity',
            'my_prefix',
            'another_manager',
            'manager',
            'repository',
            false
        );
    }
}
```

Let's explain each of these extra parameters

* 'my_prefix' will be used when defining container entries (services and 
  parameters), so with a value of `my_prefix`, we will have these values instead
  of the ones defined below
    * my_prefix.object_manager.user
    * my_prefix.object_repository.user
    * my_prefix.entity.user.class
    * my_prefix.entity.user.mapping_file
    * my_prefix.entity.user.manager
    * my_prefix.entity.user.enabled
* `another_manager` will be used as the default object manager in all defined
  entities. With the value `another_manager` the value of the parameter
  `my_prefix.entity.user.mapping_file` would be `another_manager' instead of 
  `default`. Take in account that the object_manager defined here must be
  defined as well under the Doctrine ORM configuration
* `manager` will be used as the name of the generated object manager aliases.
  With this value, we would generate a service called `my_prefix.manager.user`
  instead of `my_prefix.object_manager.user`
* `repository` will be used as the name of the generated object repository
  aliases. With this value, we would generate a service called
  `my_prefix.repository.user` instead of `my_prefix.object_manager.user`
* the last method optional boolean parameter, `false`, is the way you have to
  enable the mapping external configuration. We will see this feature in next
  chapters. By default, always `false`.
  
### Public bundles

So, what if you want to expose you bundles for everyone? And what if you want to
enable other user to overwrite the entity class, the mapping file, the object
manager assigned to this entity or even disable the entity?

Do it in 3 simple steps.

First step, define that you want to enable this feature in the last
`MappingBagProvider` parameter.

``` php
/**
 * Class MappingBagProvider.
 */
class MyBundleMappingBagProvider implements MappingBagProvider
{
    /**
     * Get mapping bag collection.
     *
     * @return MappingBagCollection
     */
    public function getMappingBagCollection() : MappingBagCollection
    {
        return MappingBagCollection::create(
            ['user' => 'User'],
            '@MyBundle',
            'MyBundle\Entity',
            'my_prefix',
            'another_manager',
            'manager',
            'repository',
            true // Change this value from false to true
        );
    }
}
```

In that case, make sure all other values are properly defined.

Second step, pass the MappingBagProvider instance to your Configuration in your
bundle class.

``` php
/**
 * Class TestMappingBundle.
 */
final class TestMappingBundle extends BaseBundle
{
    /**
     * Return a CompilerPass instance array.
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses()
    {
        return [
            new MappingCompilerPass(new MyBundleMappingBagProvider()),
        ];
    }

    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface|null The container extension
     *
     * @throws \LogicException
     */
    public function getContainerExtension()
    {
        return new TestMappingExtension(new MyBundleMappingBagProvider());
    }
}
```

Last step, in your Extension, if you don't have any configuration defined yet,
you can use a BaseConfiguration instance, so you don't need to create any extra
class. Important! Pass the MappingBagProvider saved locally as the second
parameter.

``` php
/**
 * Class TestMappingExtension.
 */
class MyBundleExtension extends BaseExtension
{
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
     * @return ConfigurationInterface|null
     */
    protected function getConfigurationInstance() : ? ConfigurationInterface
    {
        return new BaseConfiguration(
            $this->getAlias(),
            $this->mappingBagProvider
        );
    }
}
```

If you have your Extension created already, just pass the MappingBagProvider
as the second parameter. No extra classes to create, just one line.

``` php
/**
 * Class TestMappingExtension.
 */
class MyBundleExtension extends BaseExtension
{
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
     * @return ConfigurationInterface|null
     */
    protected function getConfigurationInstance() : ? ConfigurationInterface
    {
        return new MyBundleConfiguration(
            $this->getAlias(),
            $this->mappingBagProvider
        );
    }
}
```

And that's it! Now you can overwrite all your mapping values from the config 
application.

``` yml
{extension_alias}:
    mapping:
        user:
            class: "AnotherBundle\Entity\AnotherUser"
            mapping_file: "@AnotherBundle/Resources/config/doctrine/AnotherUser.orm.yml"
            manager: "another_manager"
            enabled: false
```

The *extension_alias* value will always depend on the Extension alias, and in
that case, your mapping_file value can point to a YML or XML mapping file.

### Bundles and Components

This library is useful as well when you want to change your bundle and split it
between the Symfony Bundle and the PHP Component.

After this split, make sure that your MappingBagProvider defines the new mapping
namespace properly

``` php
/**
 * Class MappingBagProvider.
 */
class MyBundleMappingBagProvider implements MappingBagProvider
{
    /**
     * Get mapping bag collection.
     *
     * @return MappingBagCollection
     */
    public function getMappingBagCollection() : MappingBagCollection
    {
        return MappingBagCollection::create(
            ['user' => 'User'],
            '@MyBundle',
            'MyOtherNamespace\Entity'
        );
    }
}
```

### Exposing your mapping without BaseBundle

You can expose your mapping without using BaseExtension and BaseConfiguration.
What these two classes do for you is just adding all the fields in your
configuration tree, validating them all and adding the resulting values in your
container as parameters, so if you want to do these steps without BaseBundle
make sure that, at the end, you have the right parameters in your container.

## Documentation extra

Some libraries will be used as well during the documentation. We encourage you
to check them all in order to increase the quality of your bundles and the way
you know them.

* [Simple Doctrine Mapping](http://github.com/mmoreram/SimpleDoctrineMapping)
* [Symfony Bundle Dependencies](http://github.com/mmoreram/symfony-bundle-dependencies)