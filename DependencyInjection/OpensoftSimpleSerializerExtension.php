<?php

/*
 * This file is part of the SimpleSerializerBundle package.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opensoft\Bundle\SimpleSerializerBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Opensoft\Bundle\SimpleSerializerBundle\Exception\RuntimeException;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class OpensoftSimpleSerializerExtension extends Extension
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // metadata
        if ('none' === $config['metadata']['cache']) {
            $container->removeAlias('opensoft_simple_serializer.metadata.cache');
        } else if ('file' === $config['metadata']['cache']) {
            $container
                ->getDefinition('opensoft_simple_serializer.metadata.cache.file')
                ->replaceArgument(0, $config['metadata']['file_cache']['dir'])
            ;

            $dir = $container->getParameterBag()->resolveValue($config['metadata']['file_cache']['dir']);
            if (!file_exists($dir)) {
                if (!$rs = @mkdir($dir, 0777, true)) {
                    throw new RuntimeException(sprintf('Could not create cache directory "%s".', $dir));
                }
            }
        } else {
            $container->setAlias('opensoft_simple_serializer.metadata.cache.file', new Alias($config['metadata']['cache'], false));
        }
        $container
            ->getDefinition('opensoft_simple_serializer.metadata.factory')
            ->replaceArgument(2, $config['metadata']['debug'])
        ;


        // directories
        $directories = array();
        $bundles = $container->getParameter('kernel.bundles');
        if ($config['metadata']['auto_detection']) {
            foreach ($bundles as $class) {
                $ref = new \ReflectionClass($class);

                $directories[$ref->getNamespaceName()] = dirname($ref->getFileName()) . '/Resources/config/simple-serializer';
            }
        }
        foreach ($config['metadata']['directories'] as $directory) {
            $directory['path'] = rtrim(str_replace('\\', '/', $directory['path']), '/');

            if ($directory['path'][0] === '@') {
                $bundleName = substr($directory['path'], 1, strpos($directory['path'], '/') - 1);

                if (!isset($bundles[$bundleName])) {
                    throw new RuntimeException(sprintf('The bundle "%s" has not been registered with AppKernel.
                    Available bundles: %s', $bundleName, implode(', ', array_keys($bundles))));
                }

                $ref = new \ReflectionClass($bundles[$bundleName]);
                $directory['path'] = dirname($ref->getFileName()) . substr($directory['path'], strlen('@'.$bundleName));
            }

            $directories[rtrim($directory['namespace_prefix'], '\\')] = rtrim($directory['path'], '\\/');
        }
        $container
            ->getDefinition('opensoft_simple_serializer.metadata.driver.file_locator')
            ->replaceArgument(0, $directories)
        ;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return null|Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->kernel->isDebug());
    }
}
