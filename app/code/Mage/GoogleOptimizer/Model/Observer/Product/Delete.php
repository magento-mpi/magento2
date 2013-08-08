<?php
/**
 * Google Experiment Product observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Product_Delete
{
    /**
     * @var Mage_GoogleOptimizer_Model_Code
     */
    protected $_modelCode;

    /**
     * @param Mage_GoogleOptimizer_Model_Code $modelCode
     */
    public function __construct(Mage_GoogleOptimizer_Model_Code $modelCode)
    {
        $this->_modelCode = $modelCode;
    }

    /**
     * Delete Product scripts after deleting product
     *
     * @param Magento_Object $observer
     * @return Mage_GoogleOptimizer_Model_Observer_Product_Delete
     */
    public function deleteProductGoogleExperimentScript($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();
        $this->_modelCode->loadByEntityIdAndType(
            $product->getId(),
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            $product->getStoreId()
        );

        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
