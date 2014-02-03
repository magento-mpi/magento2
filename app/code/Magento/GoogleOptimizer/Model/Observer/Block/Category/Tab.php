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
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @param \Magento\GoogleOptimizer\Helper\Data $helper
     * @param \Magento\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\GoogleOptimizer\Helper\Data $helper,
        \Magento\View\LayoutInterface $layout
    ) {
        $this->_helper = $helper;
        $this->_layout = $layout;
    }

    /**
     * Adds Google Experiment tab to the category edit page
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function addGoogleExperimentTab(\Magento\Event\Observer $observer)
    {
        if ($this->_helper->isGoogleExperimentActive()) {
            $block = $this->_layout->createBlock(
                'Magento\GoogleOptimizer\Block\Adminhtml\Catalog\Category\Edit\Tab\Googleoptimizer',
                'google-experiment-form'
            );

            /** @var $tabs \Magento\Catalog\Block\Adminhtml\Category\Tabs */
            $tabs = $observer->getEvent()->getTabs();
            $tabs->addTab('google-experiment-tab', array(
                'label' => __('Category View Optimization'),
                'content' => $block->toHtml(),
            ));
        }
    }
}
