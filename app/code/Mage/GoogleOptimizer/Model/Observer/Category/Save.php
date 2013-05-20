<?php
/**
 * Google Experiment Category Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Category_Save
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
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Mage_GoogleOptimizer_Helper_Data $helper
     * @param Mage_GoogleOptimizer_Model_Code $modelCode
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(
        Mage_GoogleOptimizer_Helper_Data $helper,
        Mage_GoogleOptimizer_Model_Code $modelCode,
        Mage_Core_Controller_Request_Http $request)
    {
        $this->_helper = $helper;
        $this->_modelCode = $modelCode;
        $this->_request = $request;
    }

    /**
     * Save product scripts after saving product
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleOptimizer_Model_Observer
     */
    public function saveCategoryGoogleExperimentScript($observer)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $observer->getEvent()->getCategory();

        if (!$this->_helper->isGoogleExperimentActive($category->getStoreId())) {
            return $this;
        }

        $values = $this->_request->getParam('google_experiment');
        if (!empty($values['code_id'])) {
            $this->_modelCode->load($values['code_id']);
        }

        if ($category->getId() && $values['experiment_script']) {
            $this->_modelCode
                ->setEntityType(Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CATEGORY)
                ->setEntityId($category->getId())
                ->setStoreId($category->getStoreId())
                ->setExperimentScript($values['experiment_script']);

            $this->_modelCode->save();
        }

        return $this;
    }
}
