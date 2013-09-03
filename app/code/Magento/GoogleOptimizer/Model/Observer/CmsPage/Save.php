<?php
/**
 * Google Experiment Cms Page Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_CmsPage_Save extends Magento_GoogleOptimizer_Model_Observer_SaveAbstract
{
    /**
     * @var Magento_Cms_Model_Page
     */
    protected $_page;

    /**
     * Init entity
     *
     * @param \Magento\Event\Observer $observer
     */
    protected function _initEntity($observer)
    {
        $this->_page = $observer->getEvent()->getObject();
    }

    /**
     * Get data for saving code model
     *
     * @return array
     */
    protected function _getCodeData()
    {
        return array(
            'entity_type' => Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE,
            'entity_id' => $this->_page->getId(),
            'store_id' => 0,
            'experiment_script' => $this->_params['experiment_script'],
        );
    }
}
