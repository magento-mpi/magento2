<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Obtain all carts contents for specified client
 *
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_Carts extends Magento_Backend_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Add shopping cart grid of each website
     *
     * @return Magento_Adminhtml_Block_Customer_Edit_Tab_Carts
     */
    protected function _prepareLayout()
    {
        $sharedWebsiteIds = $this->_coreRegistry->registry('current_customer')->getSharedWebsiteIds();
        $isShared = count($sharedWebsiteIds) > 1;
        foreach ($sharedWebsiteIds as $websiteId) {
            $blockName = 'customer_cart_' . $websiteId;
            $block = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Customer_Edit_Tab_Cart',
                $blockName, array('data' => array('website_id' => $websiteId)));
            if ($isShared) {
                $websiteName = $this->_storeManager->getWebsite($websiteId)->getName();
                $block->setCartHeader(__('Shopping Cart from %1', $websiteName));
            }
            $this->setChild($blockName, $block);
        }
        return parent::_prepareLayout();
    }

    /**
     * Just get child blocks html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('adminhtml_block_html_before', array('block' => $this));
        return $this->getChildHtml();
    }
}
