<?php
/**
 * Google Optimizer Observer Category Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Block_Category_Tab
{
    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @param Mage_GoogleOptimizer_Helper_Data $helper
     * @param Mage_Core_Model_Layout $layout
     */
    public function __construct(Mage_GoogleOptimizer_Helper_Data $helper, Mage_Core_Model_Layout $layout)
    {
        $this->_helper = $helper;
        $this->_layout = $layout;
    }

    /**
     * Adds Google Experiment tab to the category edit page
     *
     * @param Magento_Event_Observer $observer
     */
    public function addGoogleExperimentTab(Magento_Event_Observer $observer)
    {
        if ($this->_helper->isGoogleExperimentActive()) {
            $block = $this->_layout->createBlock(
                'Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Category_Edit_Tab_Googleoptimizer',
                'google-experiment-form'
            );

            /** @var $tabs Mage_Adminhtml_Block_Catalog_Category_Tabs */
            $tabs = $observer->getEvent()->getTabs();
            $tabs->addTab('google-experiment-tab', array(
                'label' => $this->_helper->__('Category View Optimization'),
                'content' => $block->toHtml(),
            ));
        }
    }
}
