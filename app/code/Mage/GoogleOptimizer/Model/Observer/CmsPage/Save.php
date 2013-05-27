<?php
/**
 * Google Experiment Cms Page Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_CmsPage_Save extends Mage_GoogleOptimizer_Model_Observer_SaveAbstract
{
    /**
     * @var Mage_Cms_Model_Page
     */
    protected $_page;

    /**
     * @var Varien_Event_Observer
     */
    protected $_observer;

    /**
     * Save page script after saving page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleOptimizer_Model_Observer_CmsPage_Save
     * @throws InvalidArgumentException
     */
    public function savePageGoogleExperimentScript($observer)
    {
        if (!$this->_helper->isGoogleExperimentActive()) {
            return $this;
        }

        $this->_observer = $observer;

        $this->_processSaveEvent();

        return $this;
    }

    /**
     * Save code model
     */
    protected function _saveCodeModel()
    {
        $this->_page = $this->_observer->getEvent()->getObject();

        $this->_modelCode->addData(array(
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE,
            'entity_id' => $this->_page->getId(),
            'store_id' => 0,
            'experiment_script' => $this->_params['experiment_script'],
        ));
        $this->_modelCode->save();
    }
}
