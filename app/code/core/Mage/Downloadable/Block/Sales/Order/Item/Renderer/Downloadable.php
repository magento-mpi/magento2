<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable order item render block
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Sales_Order_Item_Renderer_Downloadable extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    protected $_purchasedLinks = null;

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getLinks()
    {
            $this->_purchasedLinks = Mage::getModel('Mage_Downloadable_Model_Link_Purchased')
                ->load($this->getOrderItem()->getOrder()->getId(), 'order_id');
            $purchasedItems = Mage::getModel('Mage_Downloadable_Model_Link_Purchased_Item')->getCollection()
                ->addFieldToFilter('order_item_id', $this->getOrderItem()->getId());
            $this->_purchasedLinks->setPurchasedItems($purchasedItems);

        return $this->_purchasedLinks;
    }

    public function getLinksTitle()
    {
        if ($this->_purchasedLinks->getLinkSectionTitle()) {
            return $this->_purchasedLinks->getLinkSectionTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

}
