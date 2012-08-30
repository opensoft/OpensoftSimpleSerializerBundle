<?php
/*
 * This file is part of the OpensoftEplBundle package.
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opensoft\Bundle\SimpleSerializerBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Opensoft\Bundle\SimpleSerializerBundle\DependencyInjection\OpensoftSimpleSerializerExtension;

/**
 * OpensoftEplExtension test.
 *
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru><fightmaster>
 */
class OpensoftSimpleSerializerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;
    private $extension;

    public function testGetConfiguration()
    {
        $result = $this->extension->getConfiguration(array(), $this->container);
        $this->assertInstanceOf('Opensoft\Bundle\SimpleSerializerBundle\DependencyInjection\Configuration', $result);
    }

    public function testLoadMetadataCache()
    {
        $this->extension->load(
            array(
                'opensoft_simple_serializer' => array(
                    'metadata' => array(
                        'cache' => 'file',
                        'file_cache' => array('dir' => __DIR__ . '/../cache'),
                        'directories' => array (
                            array ('path' => '/tmp/simple-serializer', 'namespace_prefix' => '/tmp'),
                            array ('path' => '@OpensoftSimpleSerializerBundle/Resources/config', 'namespace_prefix' => 'test')
                        )
                    )
                ),
            ), $this->container);
        $definition = $this->container->getDefinition('opensoft_simple_serializer.metadata.cache.file');
        $definitionFileLocator = $this->container->getDefinition('opensoft_simple_serializer.metadata.driver.file_locator');
        $alias = $this->container->getAlias('opensoft_simple_serializer.metadata.cache');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Alias', $alias);
        $this->assertEquals(__DIR__ . '/../cache', $definition->getArgument(0));
        $dirs = $definitionFileLocator->getArgument(0);
        $this->assertCount(3, $dirs);
        $this->assertArrayHasKey('test', $dirs);
        $this->assertArrayHasKey('/tmp', $dirs);
        $this->assertArrayHasKey('Opensoft\Bundle\SimpleSerializerBundle', $dirs);
        $this->assertStringEndsWith('OpensoftSimpleSerializerBundle/Resources/config/simple-serializer', $dirs['Opensoft\Bundle\SimpleSerializerBundle']);
        $this->assertEquals('/tmp/simple-serializer', $dirs['/tmp']);
        $this->assertStringEndsWith('OpensoftSimpleSerializerBundle/Resources/config', $dirs['test']);
    }

    /**
     * @expectedException Opensoft\Bundle\SimpleSerializerBundle\Exception\RuntimeException
     */
    public function testLoadMetadataCacheRuntimeExceptionParseDir()
    {
        $this->extension->load(
            array(
                'opensoft_simple_serializer' => array(
                    'metadata' => array(
                        'cache' => 'file',
                        'file_cache' => array('dir' => '/tmp'),
                        'directories' => array (
                            array ('path' => '@Opensoft/Resource/config', 'namespace_prefix' => 'test')
                        )
                    )
                ),
            ), $this->container);
    }

    /**
     * @expectedException Opensoft\Bundle\SimpleSerializerBundle\Exception\RuntimeException
     */
    public function testLoadMetadataCacheRuntimeException()
    {
        $this->extension->load(
            array(
                'opensoft_simple_serializer' => array(
                    'metadata' => array(
                        'cache' => 'file',
                        'file_cache' => array('dir' => '/tmpRes')
                    )
                ),
            ), $this->container);
    }

    public function testLoadMetadataCacheRemoveAlias()
    {
        $this->extension->load(
            array(
                'opensoft_simple_serializer' => array(
                    'metadata' => array(
                        'cache' => 'none',
                        'file_cache' => array('dir' => '/tmp')
                    )
                ),
            ), $this->container);
        $this->assertFalse($this->container->hasAlias('opensoft_simple_serializer.metadata.cache'));

    }

    public function testLoadMetadataCacheSetAlias()
    {
        $this->extension->load(
            array(
                'opensoft_simple_serializer' => array(
                    'metadata' => array(
                        'cache' => 'memcache',
                        'file_cache' => array('dir' => '/tmp')
                    )
                ),
            ), $this->container);
        $this->assertFalse($this->container->getAlias('opensoft_simple_serializer.metadata.cache.file')->isPublic());

    }

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel
            ->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue(array('OpensoftSimpleSerializerBundle' => 'Opensoft\Bundle\SimpleSerializerBundle\OpensoftSimpleSerializerBundle')
        ))
        ;
        $kernel
            ->expects($this->any())
            ->method('isDebug')
            ->will($this->returnValue(false))
        ;
        $this->extension = new OpensoftSimpleSerializerExtension($kernel);
        $this->container->setParameter('kernel.bundles', $kernel->getBundles());
    }

    protected function tearDown()
    {
        unset($this->container, $this->extension);
    }
}
