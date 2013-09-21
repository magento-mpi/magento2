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
 * Downloadable order item render block
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Block_Sales_Order_Item_Renderer_Downloadable extends Magento_Sales_Block_Order_Item_Renderer_Default
{
    protected $_purchasedLinks;

    /**
     * @var Magento_Downloadable_Model_Link_PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var Magento_Downloadable_Model_Resource_Link_Purchased_Item_Collection
     */
    protected $_itemsFactory;

    /**
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Catalog_Model_Product_OptionFactory $productOptionFactory
     * @param Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory
     * @param Magento_Downloadable_Model_Resource_Link_Purchased_Item_Collection $itemsFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_String $coreString,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Catalog_Model_Product_OptionFactory $productOptionFactory,
        Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory,
        Magento_Downloadable_Model_Resource_Link_Purchased_Item_Collection $itemsFactory,
        array $data = array()
    ) {
        $this->_purchasedFactory = $purchasedFactory;
        $this->_itemsFactory = $itemsFactory;
        parent::__construct($coreString, $coreData, $context, $productOptionFactory, $data);
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getLinks()
    {
        $this->_purchasedLinks = $this->_purchasedFactory->create()
            ->load($this->getOrderItem()->getOrder()->getId(), 'order_id');
        $purchasedItems = $this->_itemsFactory->create()
            ->addFieldToFilter('order_item_id', $this->getOrderItem()->getId());
        $this->_purchasedLinks->setPurchasedItems($purchasedItems);

        return $this->_purchasedLinks;
    }

    public function getLinksTitle()
    {
        if ($this->_purchasedLinks->getLinkSectionTitle()) {
            return $this->_purchasedLinks->getLinkSectionTitle();
        }
        return $this->_storeConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

}
