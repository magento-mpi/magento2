<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerFactory;
use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface;

class Composite implements HandlerInterface
{
    /**
     * @var HandlerInterface[]
     */
    protected $handlers;

    /**
     * @param HandlerFactory $factory
     * @param array $handlers
     */
    public function __construct(HandlerFactory $factory, array $handlers = array())
    {
        foreach ($handlers as $instance) {
            $this->handlers[] = $factory->create($instance);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function handle(\Magento\Catalog\Model\Product $product)
    {
        foreach ($this->handlers as $constructor) {
            $constructor->handle($product);
        }
    }
} 
