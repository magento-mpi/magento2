<?php

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;

class Widget extends Block implements ContainerInterface
{
    const TYPE = 'widget';
}
