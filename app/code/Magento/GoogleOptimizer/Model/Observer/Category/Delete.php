<?php
/**
 * Google Experiment Category Delete observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_Category_Delete
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
     * @return Magento_GoogleOptimizer_Model_Observer_Category_Delete
     */
    public function deleteCategoryGoogleExperimentScript($observer)
    {
        /** @var $category Magento_Catalog_Model_Category */
        $category = $observer->getEvent()->getCategory();
        $this->_modelCode->loadByEntityIdAndType(
            $category->getId(),
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY,
            $category->getStoreId()
        );
        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
