<?php

/*
 * This file is part of the SimpleSerializerBundle package.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opensoft\Bundle\SimpleSerializerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;
use Opensoft\Bundle\SimpleSerializerBundle\DependencyInjection\OpensoftSimpleSerializerExtension;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class OpensoftSimpleSerializerBundle extends Bundle
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
     * @return OpensoftSimpleSerializerExtension
     */
    public function getContainerExtension()
    {
        return new OpensoftSimpleSerializerExtension($this->kernel);
    }
}
