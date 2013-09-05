<?php
/**
 * Google Experiment Category Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_Category_Save extends Magento_GoogleOptimizer_Model_Observer_SaveAbstract
{
    /**
     * @var Magento_Catalog_Model_Category
     */
    protected $_category;

    /**
     * Init entity
     *
     * @param \Magento\Event\Observer $observer
     */
    protected function _initEntity($observer)
    {
        $this->_category = $observer->getEvent()->getCategory();
    }

    /**
     * Check is Google Experiment enabled
     */
    protected function _isGoogleExperimentActive()
    {
        return $this->_helper->isGoogleExperimentActive($this->_category->getStoreId());
    }

    /**
     * Get data for saving code model
     *
     * @return array
     */
    protected function _getCodeData()
    {
        return array(
            'entity_type' => Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY,
            'entity_id' => $this->_category->getId(),
            'store_id' => $this->_category->getStoreId(),
            'experiment_script' => $this->_params['experiment_script'],
        );
    }
}
