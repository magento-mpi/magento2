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
namespace Magento\Adminhtml\Block\Customer\Edit\Tab;

class Carts extends \Magento\Adminhtml\Block\Template
{
    /**
     * Add shopping cart grid of each website
     *
     * @return \Magento\Adminhtml\Block\Customer\Edit\Tab\Carts
     */
    protected function _prepareLayout()
    {
        $sharedWebsiteIds = \Mage::registry('current_customer')->getSharedWebsiteIds();
        $isShared = count($sharedWebsiteIds) > 1;
        foreach ($sharedWebsiteIds as $websiteId) {
            $blockName = 'customer_cart_' . $websiteId;
            $block = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Customer\Edit\Tab\Cart',
                $blockName, array('data' => array('website_id' => $websiteId)));
            if ($isShared) {
                $block->setCartHeader(__('Shopping Cart from %1', \Mage::app()->getWebsite($websiteId)->getName()));
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
        \Mage::dispatchEvent('adminhtml_block_html_before', array('block' => $this));
        return $this->getChildHtml();
    }
}
