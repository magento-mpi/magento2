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
    /**
     * @var Magento_Downloadable_Model_Link_Purchased
     */
    protected $_purchased;

    /**
     * @var Magento_Downloadable_Model_Link_PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory
     */
    protected $_itemsFactory;

    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory,
        Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory $itemsFactory,
        array $data = array()
    ) {
        $this->_purchasedFactory = $purchasedFactory;
        $this->_itemsFactory = $itemsFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getLinks()
    {
        $this->_purchased = $this->_purchasedFactory->create()->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedLinks = $this->_itemsFactory->create()->addFieldToFilter('order_item_id', $this->getItem()->getId());
        $this->_purchased->setPurchasedItems($purchasedLinks);

        return $this->_purchased;
    }

    public function getLinksTitle()
    {
        if ($this->_purchased->getLinkSectionTitle()) {
            return $this->_purchased->getLinkSectionTitle();
        }
        return $this->_storeConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
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
