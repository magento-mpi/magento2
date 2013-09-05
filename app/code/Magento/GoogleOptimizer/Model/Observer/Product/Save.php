<?php
/**
 * Google Experiment Product Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_Product_Save extends Magento_GoogleOptimizer_Model_Observer_SaveAbstract
{
    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Init entity
     *
     * @param \Magento\Event\Observer $observer
     */
    protected function _initEntity($observer)
    {
        $this->_product = $observer->getEvent()->getProduct();
    }

    /**
     * Check is Google Experiment enabled
     */
    protected function _isGoogleExperimentActive()
    {
        return $this->_helper->isGoogleExperimentActive($this->_product->getStoreId());
    }

    /**
     * Get data for saving code model
     *
     * @return array
     */
    protected function _getCodeData()
    {
        return array(
            'entity_type' => Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            'entity_id' => $this->_product->getId(),
            'store_id' => $this->_product->getStoreId(),
            'experiment_script' => $this->_params['experiment_script'],
        );
    }
}
