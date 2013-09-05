<?php
/**
 * Google Experiment Cms Page Delete observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_CmsPage_Delete
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
     * @return Magento_GoogleOptimizer_Model_Observer_CmsPage_Delete
     */
    public function deleteCmsGoogleExperimentScript($observer)
    {
        /** @var $cmsPage Magento_Cms_Model_Page */
        $cmsPage = $observer->getEvent()->getObject();
        $this->_modelCode->loadByEntityIdAndType(
            $cmsPage->getId(),
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE
        );
        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
