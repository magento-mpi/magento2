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
 * Sales Order downloadable items name column renderer
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Block_Adminhtml_Sales_Items_Column_Downloadable_Name extends Magento_Adminhtml_Block_Sales_Items_Column_Name
{
    protected $_purchased = null;
    public function getLinks()
    {
        $this->_purchased = Mage::getModel('Magento_Downloadable_Model_Link_Purchased')
            ->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedItem = Mage::getModel('Magento_Downloadable_Model_Link_Purchased_Item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getId());
        $this->_purchased->setPurchasedItems($purchasedItem);
        return $this->_purchased;
    }

    public function getLinksTitle()
    {
        if ($this->_purchased && $this->_purchased->getLinkSectionTitle()) {
            return $this->_purchased->getLinkSectionTitle();
        }
        return $this->_coreStoreConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }
}
?>
