<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Helper_Product_ConfigurationPool
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Catalog_Helper_Product_Configuration_Interface[]
     */
    private $_instances = array();

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @return Magento_Catalog_Helper_Product_Configuration_Interface
     * @throws LogicException
     */
    public function get($className)
    {
        if (!isset($this->_instances[$className])) {
            /** @var Magento_Catalog_Helper_Product_Configuration_Interface $helperInstance */
            $helperInstance = $this->_objectManager->get($className);
            if (false === ($helperInstance instanceof Magento_Catalog_Helper_Product_Configuration_Interface)) {
                throw new LogicException(
                    "{$className} doesn't implement Magento_Catalog_Helper_Product_Configuration_Interface"
                );
            }
            $this->_instances[$className] = $helperInstance;
        }
        return $this->_instances[$className];
    }
}
