<?php
/**
 * Google Experiment Product observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_Product_Delete
{
    /**
     * @var Magento_GoogleOptimizer_Model_Code
     */
    protected $_modelCode;

    /**
     * @param Magento_GoogleOptimizer_Model_Code $modelCode
     */
    public function __construct(Magento_GoogleOptimizer_Model_Code $modelCode)
    {
        $this->_modelCode = $modelCode;
    }

    /**
     * Delete Product scripts after deleting product
     *
     * @param \Magento\Object $observer
     * @return Magento_GoogleOptimizer_Model_Observer_Product_Delete
     */
    public function deleteProductGoogleExperimentScript($observer)
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();
        $this->_modelCode->loadByEntityIdAndType(
            $product->getId(),
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            $product->getStoreId()
        );

        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
