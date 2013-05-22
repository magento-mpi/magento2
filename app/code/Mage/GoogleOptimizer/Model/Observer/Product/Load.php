<?php
/**
 * Google Experiment Product observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Product_Load
{
    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_GoogleOptimizer_Model_Code
     */
    protected $_modelCode;

    /**
     * @param Mage_GoogleOptimizer_Helper_Data $helper
     * @param Mage_GoogleOptimizer_Model_Code $modelCode
     */
    public function __construct(
        Mage_GoogleOptimizer_Helper_Data $helper,
        Mage_GoogleOptimizer_Model_Code $modelCode
    ) {
        $this->_helper = $helper;
        $this->_modelCode = $modelCode;
    }

    /**
     * Loading product scripts after load product
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleOptimizer_Model_Observer_Product_Load
     */
    public function appendToProductGoogleExperimentScript($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        if (!$this->_helper->isGoogleExperimentActive($product->getStoreId())) {
            return $this;
        }

        $this->_modelCode->loadByEntityIdAndType(
            $product->getId(),
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            $product->getStoreId()
        );

        if ($this->_modelCode->getId()) {
            $product->setGoogleExperiment($this->_modelCode);
        }

        return $this;
    }
}
