<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

class HandlerFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create handler instance
     *
     * @param string $instance
     * @param array $arguments
     * @return object
     * @throws \InvalidArgumentException
     */
    public function create($instance, array $arguments = array())
    {

        if (!is_subclass_of(
            $instance,
            '\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\HandlerInterface')
        ) {
            throw new \InvalidArgumentException(
                $instance . ' does not implement '
                 . 'Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\HandlerInterface'
            );
        }

        return $this->objectManager->create($instance, $arguments);
    }
} 
