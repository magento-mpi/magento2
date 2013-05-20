<?php
/**
 * Google Experiment Cms Page Delete observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_CmsPage_Delete
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
    public function deleteCmsGoogleExperimentScript($observer)
    {
        /** @var $cmsPage Mage_Cms_Model_Page */
        $cmsPage = $observer->getEvent()->getObject();
        $this->_modelCode->loadScripts($cmsPage->getId(), Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CMS);
        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
