<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product option factory
 */
class Magento_Catalog_Model_Product_Option_Type_Factory
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
     * Create product option
     *
     * @param string $className
     * @param array $data
     * @return Magento_Catalog_Model_Product_Option_Type_Default
     * @throws Magento_Core_Exception
     */
    public function create($className, array $data = array())
    {
        $option = $this->_objectManager->create($className, $data);

        if (!$option instanceof Magento_Catalog_Model_Product_Option_Type_Default) {
            throw new Magento_Core_Exception($className
                . ' doesn\'t extends Magento_Catalog_Model_Product_Option_Type_Default');
        }
        return $option;
    }
}
