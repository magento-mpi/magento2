<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Config_Source_Class_Product implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Tax_Model_Class_Source_ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Magento_Tax_Model_Class_Source_ProductFactory $productFactory
     */
    public function __construct(Magento_Tax_Model_Class_Source_ProductFactory $productFactory)
    {
        $this->_productFactory = $productFactory;
    }

    /**
     * Retrieve list of products
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $sourceProduct Magento_Tax_Model_Class_Source_Product */
        $sourceProduct = $this->_productFactory->create();
        return $sourceProduct->toOptionArray();
    }
}
