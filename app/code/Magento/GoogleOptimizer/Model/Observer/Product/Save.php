<?php
/**
 * Google Experiment Product Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer\Product;

class Save extends \Magento\GoogleOptimizer\Model\Observer\AbstractSave
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Init entity
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    protected function _initEntity($observer)
    {
        $this->_product = $observer->getEvent()->getProduct();
    }

    /**
     * Check is Google Experiment enabled
     *
     * @return bool
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
            'entity_type' => \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PRODUCT,
            'entity_id' => $this->_product->getId(),
            'store_id' => $this->_product->getStoreId(),
            'experiment_script' => $this->_params['experiment_script'],
        );
    }
}
