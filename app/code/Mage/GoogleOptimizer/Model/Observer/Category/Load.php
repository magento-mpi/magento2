<?php
/**
 * Google Experiment Category Load observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Category_Load
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
     * @return Mage_GoogleOptimizer_Model_Observer_Category_Load
     */
    public function appendToCategoryGoogleExperimentScript($observer)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $observer->getEvent()->getCategory();

        if (!$this->_helper->isGoogleExperimentActive($category->getStoreId())) {
            return $this;
        }

        $this->_modelCode->loadScripts(
            $category->getId(),
            Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CATEGORY,
            $category->getStoreId()
        );

        if ($this->_modelCode->getId()) {
            $category->setGoogleExperiment($this->_modelCode);
        }

        return $this;
    }
}
