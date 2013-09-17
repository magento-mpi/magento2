<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Helper\Product;

class ConfigurationPool
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Helper\Product\Configuration\Interface[]
     */
    private $_instances = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @return \Magento\Catalog\Helper\Product\Configuration\Interface
     * @throws \LogicException
     */
    public function get($className)
    {
        if (!isset($this->_instances[$className])) {
            /** @var \Magento\Catalog\Helper\Product\Configuration\Interface $helperInstance */
            $helperInstance = $this->_objectManager->get($className);
            if (false === ($helperInstance instanceof \Magento\Catalog\Helper\Product\Configuration\Interface)) {
                throw new \LogicException(
                    "{$className} doesn't implement \Magento\Catalog\Helper\Product\Configuration\Interface"
                );
            }
            $this->_instances[$className] = $helperInstance;
        }
        return $this->_instances[$className];
    }
}
