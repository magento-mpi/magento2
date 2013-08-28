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
 * Order Downloadable Pdf Items renderer
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Downloadable_Model_Sales_Order_Pdf_Items_Abstract extends Magento_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Downloadable links purchased model
     *
     * @var Magento_Downloadable_Model_Link_Purchased
     */
    protected $_purchasedLinks = null;

    /**
     * Return Purchased link for order item
     *
     * @return Magento_Downloadable_Model_Link_Purchased
     */
    public function getLinks()
    {
        $this->_purchasedLinks = Mage::getModel('Magento_Downloadable_Model_Link_Purchased')
            ->load($this->getOrder()->getId(), 'order_id');
        $purchasedItems = Mage::getModel('Magento_Downloadable_Model_Link_Purchased_Item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getOrderItem()->getId());
        $this->_purchasedLinks->setPurchasedItems($purchasedItems);

        return $this->_purchasedLinks;
    }

    /**
     * Return Links Section Title for order item
     *
     * @return string
     */
    public function getLinksTitle()
    {
        if ($this->_purchasedLinks->getLinkSectionTitle()) {
            return $this->_purchasedLinks->getLinkSectionTitle();
        }
        return Mage::getStoreConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }
}
