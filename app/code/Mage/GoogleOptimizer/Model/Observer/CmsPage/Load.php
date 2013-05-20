<?php
/**
 * Google Experiment Cms Page Delete observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_CmsPage_Load
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
     * @return Mage_GoogleOptimizer_Model_Observer_CmsPage_Load
     */
    public function appendToCmsPageGoogleExperimentScript($observer)
    {
        /** @var $cmsPage Mage_Cms_Model_Page */
        $cmsPage = $observer->getEvent()->getObject();

        if (!$this->_helper->isGoogleExperimentActive()) {
            return $this;
        }

        $this->_modelCode->loadScripts(
            $cmsPage->getId(),
            Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CMS
        );

        if ($this->_modelCode->getId()) {
            $cmsPage->setGoogleExperiment($this->_modelCode);
        }

        return $this;
    }
}
