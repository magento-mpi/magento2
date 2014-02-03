<?php
/**
 * Google Experiment Cms Page Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer\CmsPage;

class Save extends \Magento\GoogleOptimizer\Model\Observer\AbstractSave
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_page;

    /**
     * Init entity
     *
     * @param \Magento\Event\Observer $observer
     * @return void
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
            'entity_type' => \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PAGE,
            'entity_id' => $this->_page->getId(),
            'store_id' => 0,
            'experiment_script' => $this->_params['experiment_script'],
        );
    }
}
