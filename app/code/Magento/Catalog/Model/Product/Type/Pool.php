<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product type factory
 */
class Magento_Catalog_Model_Product_Type_Pool
{
    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Gets product of particular type
     *
     * @param string $className
     * @param array $data
     * @return Magento_Catalog_Model_Product_Type_Abstract
     * @throws Magento_Core_Exception
     */
    public function get($className, array $data = array())
    {
        $product = $this->_objectManager->get($className, $data);

        if (!$product instanceof Magento_Catalog_Model_Product_Type_Abstract) {
            throw new Magento_Core_Exception($className
                . ' doesn\'t extends Magento_Catalog_Model_Product_Type_Abstract');
        }
        return $product;
    }
}
