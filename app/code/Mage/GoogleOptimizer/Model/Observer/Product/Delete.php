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
    public function __construct(
        Mage_GoogleOptimizer_Model_Code $modelCode
    ) {
        $this->_modelCode = $modelCode;
    }

    /**
     * Delete Product scripts after deleting product
     *
     * @param Varien_Object $observer
     * @return Mage_GoogleOptimizer_Model_Observer
     */
    public function deleteProductGoogleExperimentScript($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();
        $this->_modelCode->loadScripts(
            $product->getId(),
            Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_PRODUCT,
            $product->getStoreId()
        );

        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
