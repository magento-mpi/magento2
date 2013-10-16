<?php

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;

class Remove extends Container implements ContainerInterface
{
    const TYPE = 'remove';

    public function register(ContainerInterface $parent = null)
    {
        //var_dump($this->meta['name']);
        $this->removeElement($this->meta['name']);
    }
}
