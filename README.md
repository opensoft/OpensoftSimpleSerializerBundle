OpensoftSimpleSerializerBundle
==============================

Introduction
------------

OpensoftSimpleSerializerBundle is wrapper for <a href="https://github.com/opensoft/simple-serializer">simple-serializer</a> library.

Installation
------------

Using Composer (recommended)
----------------------------

To install OpensoftSimpleSerializerBundle with Composer just add the following to your composer.json file:

```yml
// composer.json
{
    // ...
    require: {
        // ...
        "opensoft/opensoft-simple-serializer-bundle": "dev-master"
    }
}
```

Then, you can install the new dependencies by running Composer’s update command from the directory
where your composer.json file is located:

```bash
$ php composer.phar update
```

Composer will automatically download all required files, and install them for you.
All that is left to do is to update your AppKernel.php file, and register the new bundle:

```php
<?php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Opensoft\SimpleSerializerBundle\OpensoftSimpleSerializerBundle($this),
    // ...
);
```

Using the deps file (Symfony 2.0.x)
-----------------------------------

Update your deps file

```yml
### SimpleSerializer library
[simple-serializer]
    git=git://github.com/opensoft/simple-serializer.git
### SimpleSerializer bundle
[OpensoftSimpleSerializerBundle]
    git=git://github.com/opensoft/OpensoftSimpleSerializerBundle.git
    target=bundles/Opensoft/Bundle/SimpleSerializerBundle
```

Update your AppKernel.php file, and register the new bundle:

```php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Opensoft\Bundle\SimpleSerializerBundle\OpensoftSimpleSerializerBundle($this),
    // ...
);
```

Make sure that you also register the namespaces with the autoloader:

```php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Opensoft'  => __DIR__.'/../vendor/bundles',
    'Opensoft\\SimpleSerializer' => __DIR__.'/../vendor/simple-serializer/src',
    // ...
));
```

Now use the vendors script to clone the newly added repositories into your project:

```bash
$ php bin/vendors install
```


Configuration
-------------

OpensoftSimpleSerializerBundle requires no initial configuration to get you started.

Below you find a reference of all configuration options with their default values:

```yml
# config.yml
opensoft_simple_serializer:
    metadata:
        cache: file
        debug: "%kernel.debug%"
        file_cache:
            dir: "%kernel.cache_dir%/simple-serializer"
        # Using auto-detection, the mapping files for each bundle will be
        # expected in the Resources/config/simple-serializer directory.
        #
        # Example:
        # class: My\FooBundle\Entity\User
        # expected path: @MyFooBundle/Resources/config/simple-serializer/Entity.User.(yml|xml|php)
        auto_detection: true
        # if you don't want to use auto-detection, you can also define the
        # namespace prefix and the corresponding directory explicitly
        directories:
            any-name:
                namespace_prefix: "My\\FooBundle"
                path: "@MyFooBundle/Resources/config/simple-serializer"
            another-name:
                namespace_prefix: "My\\BarBundle"
                path: "@MyBarBundle/Resources/config/simple-serializer"
```

Usage
-----

Firstly, you could create mapping files for your objects.

```yml
# MyBundle\Resources\config\serializer\ClassName.yml
Fully\Qualified\ClassName:
    properties:
        some-property:
            expose: false
            type: string
            serialized_name: foo
```

Below you find a reference of all configuration options for property:

* expose
 * true
 * false (default)
* type
 * integer
 * boolean
 * double
 * string
 * array
 * T - fully qualified class name
 * array<T>
 * DateTime (default format is ISO8601)
 * DateTime<format>
  * format could be name of DateTime constant (COOKIE, ISO8601) or string
* serialized_name
 * default value is equal name property

Then you could use "simple_serializer" service.


```php
<?php
//serialization
$serializer = $container->get('simple_serializer');
$data = $serializer->serialize($object);
//deserialization
$object = $serializer->unserialize($data, $object);
```
