# Base Bundle

[![Build Status](https://travis-ci.org/mmoreram/BaseBundle.png?branch=master)](https://travis-ci.org/mmoreram/BaseBundle)

-----

> The minimum requirements of this bundle is **PHP 8.0** and **Symfony 5.1** 
> because the bundle is using features on both versions. If you're not using
> them yet, I encourage you to do it.

## About the content

This bundle aims to be the base for all bundles in your Symfony project. Know
about these big blocks.

**Bundles**

* [Bundle extension](#bundle-extension)
    * [Bundle dependencies](#bundle-dependencies)
    * [Extension declaration](#extension-declaration)
    * [Commands declaration](#commands-declaration)
    * [SimpleBaseBundle](#simplebasebundle)
* [Extension](#extension)
    * [Extending BaseExtension](#extending-baseextension)
* [Configuration](#configuration)
    * [Extension alias](#extension-alias)
    * [Extending BaseConfiguration](#extending-baseconfiguration)

**Functional Tests**

* [Functional Tests](#functional-tests)
    * [BaseKernel](#basekernel)
    * [BaseFunctionalTest](#basefuncionaltest)
    * [Fast testing methods](#fast-testing-methods)

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
use Mmoreram\BaseBundle\BaseBundle;

/**
 * My bundle
 */
final class MyBundle extends BaseBundle
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
use Mmoreram\BaseBundle\BaseBundle;

/**
 * My bundle
 */
final class MyBundle extends BaseBundle
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
instances. By default, this method returns empty array.

``` php
/**
 * Class AbstractBundle.
 */
abstract class BaseBundle extends Bundle
{
    // ...

    /**
     * Get command instance array
     *
     * @return Command[]
     */
    public function getCommands() : array
    {
        return [];
    }

    // ...
}
```

I highly recommend you to never use Commands with this kind of magic, as
commands should be, as Controllers and EventListeners, only an entry point to
your domain. You can define your commands as services, injecting there all you
need to make it work.

[How to define commands as services](http://symfony.com/doc/current/cookbook/console/commands_as_services.html)

### SimpleBaseBundle

Even simpler.

Symfony should provide a RAD infrastructure that, in case you want to create a 
rapid bundle exposing an essential parts to the framework, didn't make you spend
too much time and effort on that.

So, for your RAD applications, do you really think you need more than one single
class to create a simple bundle? Not at all. Not anymore.

Please, welcome SimpleBaseBundle, a simple way of creating Bundles with one
class for your RAD applications.

``` php
use Mmoreram\BaseBundle\Mapping\MappingBagProvider;
use Mmoreram\BaseBundle\SimpleBaseBundle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class TestSimpleBundle
 */
class TestSimpleBundle extends SimpleBaseBundle
{
    /**
     * get config files
     */
    public function getConfigFiles() : array
    {
        return [
            'services'
        ];
    }
    
    /**
     * Get command instance array
     *
     * @return Command[]
     */
    public function getCommands() : array
    {
        return [];
    }

    /**
     * Return a CompilerPass instance array.
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses()
    {
        return [];
    }

    /**
     * Create instance of current bundle, and return dependent bundle namespaces.
     *
     * @return array Bundle instances
     */
    public static function getBundleDependencies(KernelInterface $kernel)
    {
        return [];
    }
}
```

and that's it. 

With this class, you will create the bundle with its dependencies, you will
initialize the commands and the Compiler Passes if needed, you will load the
yaml config files and you will initialize the entities with the given
configuration defined in the MappingBagProvider.

No need to create a DependencyInjection folder.

If your project takes another dimension or quality degree, then feel free to
change your bundle implementation and start extending BaseBundle instead of
SimpleBaseBundle. Then, create the needed DependencyInjection folder.

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
use Mmoreram\BaseBundle\Kernel\BaseKernel;
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;

/**
 * Class MyTest.
 */
final class MyTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
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
`var/test` folder and creates a unique `{hash}` folder
inside.

Each time you reuse the same kernel configuration, this previous generated cache
will be used in order to increase the performance of the tests.

To increase *much more* this performance, don't hesitate to create a tmpfs
inside this `var/test/` folder by using this command. 

``` bash
sudo mount -t tmpfs -o size=512M tmpfs var/test/
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
    protected static function getKernel() : KernelInterface
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
    $this->has('service_name')
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
