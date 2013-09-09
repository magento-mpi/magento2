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
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager_Proxy
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Add shopping cart grid of each website
     *
     * @return Magento_Adminhtml_Block_Customer_Edit_Tab_Carts
     */
    protected function _prepareLayout()
    {
        $sharedWebsiteIds = Mage::registry('current_customer')->getSharedWebsiteIds();
        $isShared = count($sharedWebsiteIds) > 1;
        foreach ($sharedWebsiteIds as $websiteId) {
            $blockName = 'customer_cart_' . $websiteId;
            $block = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Customer_Edit_Tab_Cart',
                $blockName, array('data' => array('website_id' => $websiteId)));
            if ($isShared) {
                $block->setCartHeader(__('Shopping Cart from %1', Mage::app()->getWebsite($websiteId)->getName()));
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
