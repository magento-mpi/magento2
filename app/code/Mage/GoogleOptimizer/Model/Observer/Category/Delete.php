<?php
/**
 * Google Experiment Category Delete observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Category_Delete
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
     * @param Varien_Object $observer
     * @return Mage_GoogleOptimizer_Model_Observer
     */
    public function deleteCategoryGoogleExperimentScript($observer)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $observer->getEvent()->getCategory();
        $this->_modelCode->loadScripts(
            $category->getId(),
            Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CATEGORY,
            $category->getStoreId()
        );
        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
