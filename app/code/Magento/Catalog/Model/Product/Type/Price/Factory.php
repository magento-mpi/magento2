<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product type price factory
 */
class Magento_Catalog_Model_Product_Type_Price_Factory
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
     * Create price model for product of particular type
     *
     * @param string $className
     * @param array $data
     * @return Magento_Catalog_Model_Product_Type_Price
     * @throws Magento_Core_Exception
     */
    public function create($className, array $data = array())
    {
        $price = $this->_objectManager->create($className, $data);

        if (!$price instanceof Magento_Catalog_Model_Product_Type_Price) {
            throw new Magento_Core_Exception($className
                . ' doesn\'t extends Magento_Catalog_Model_Product_Type_Price');
        }
        return $price;
    }
}
