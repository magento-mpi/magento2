<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\HandlerInterface;
use Magento\Catalog\Model\Product;

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
     * @param Product $product
     * @return void
     */
    public function handle(Product $product)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($product);
        }
    }
} 
