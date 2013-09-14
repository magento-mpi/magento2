<?php
/**
 * Google Optimizer Observer Category Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer\Block\Category;

class Tab
{
    /**
     * @var \Magento\GoogleOptimizer\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @param \Magento\GoogleOptimizer\Helper\Data $helper
     * @param \Magento\Core\Model\Layout $layout
     */
    public function __construct(\Magento\GoogleOptimizer\Helper\Data $helper, \Magento\Core\Model\Layout $layout)
    {
        $this->_helper = $helper;
        $this->_layout = $layout;
    }

    /**
     * Adds Google Experiment tab to the category edit page
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addGoogleExperimentTab(\Magento\Event\Observer $observer)
    {
        if ($this->_helper->isGoogleExperimentActive()) {
            $block = $this->_layout->createBlock(
                'Magento\GoogleOptimizer\Block\Adminhtml\Catalog\Category\Edit\Tab\Googleoptimizer',
                'google-experiment-form'
            );

            /** @var $tabs \Magento\Adminhtml\Block\Catalog\Category\Tabs */
            $tabs = $observer->getEvent()->getTabs();
            $tabs->addTab('google-experiment-tab', array(
                'label' => __('Category View Optimization'),
                'content' => $block->toHtml(),
            ));
        }
    }
}
