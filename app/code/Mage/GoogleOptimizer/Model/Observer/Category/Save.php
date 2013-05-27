<?php
/**
 * Google Experiment Category Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Category_Save extends Mage_GoogleOptimizer_Model_Observer_SaveAbstract
{
    /**
     * @var Mage_Catalog_Model_Category
     */
    protected $_category;

    /**
     * Save category script after saving category
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleOptimizer_Model_Observer_Category_Save
     * @throws InvalidArgumentException
     */
    public function saveCategoryGoogleExperimentScript($observer)
    {
        $this->_category = $observer->getEvent()->getCategory();

        if (!$this->_helper->isGoogleExperimentActive($this->_category->getStoreId())) {
            return $this;
        }

        $this->_processSaveEvent();

        return $this;
    }

    /**
     * Save code model
     */
    protected function _saveCodeModel()
    {
        $this->_modelCode->addData(array(
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY,
            'entity_id' => $this->_category->getId(),
            'store_id' => $this->_category->getStoreId(),
            'experiment_script' => $this->_params['experiment_script'],
        ));
        $this->_modelCode->save();
    }
}
