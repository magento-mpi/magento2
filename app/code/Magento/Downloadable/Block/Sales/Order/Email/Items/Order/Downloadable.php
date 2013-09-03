<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Downloadable Sales Order Email items renderer
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Block_Sales_Order_Email_Items_Order_Downloadable extends Magento_Sales_Block_Order_Email_Items_Order_Default
{
    protected $_purchased = null;

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getLinks()
    {
        $this->_purchased = Mage::getModel('Magento_Downloadable_Model_Link_Purchased')
            ->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedLinks = Mage::getModel('Magento_Downloadable_Model_Link_Purchased_Item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getId());
        $this->_purchased->setPurchasedItems($purchasedLinks);

        return $this->_purchased;
    }

    public function getLinksTitle()
    {
        if ($this->_purchased->getLinkSectionTitle()) {
            return $this->_purchased->getLinkSectionTitle();
        }
        return $this->_coreStoreConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    public function getPurchasedLinkUrl($item)
    {
        return $this->getUrl('downloadable/download/link', array(
            'id'        => $item->getLinkHash(),
            '_store'    => $this->getOrder()->getStore(),
            '_secure'   => true,
            '_nosid'    => true
        ));
    }
}
