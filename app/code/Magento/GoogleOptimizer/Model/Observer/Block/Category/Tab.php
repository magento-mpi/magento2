<?php
/**
 * Google Optimizer Observer Category Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_Block_Category_Tab
{
    /**
     * @var Magento_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @param Magento_GoogleOptimizer_Helper_Data $helper
     * @param Magento_Core_Model_Layout $layout
     */
    public function __construct(Magento_GoogleOptimizer_Helper_Data $helper, Magento_Core_Model_Layout $layout)
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
                'Magento_GoogleOptimizer_Block_Adminhtml_Catalog_Category_Edit_Tab_Googleoptimizer',
                'google-experiment-form'
            );

            /** @var $tabs Magento_Adminhtml_Block_Catalog_Category_Tabs */
            $tabs = $observer->getEvent()->getTabs();
            $tabs->addTab('google-experiment-tab', array(
                'label' => __('Category View Optimization'),
                'content' => $block->toHtml(),
            ));
        }
    }
}
