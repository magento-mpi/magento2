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

    /**
     * @var Magento_Downloadable_Model_Link_PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Catalog_Model_Product_OptionFactory $optionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory
     * @param Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory $itemsFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_String $coreString,
        Magento_Catalog_Model_Product_OptionFactory $optionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory,
        Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory $itemsFactory,
        array $data = array()
    ) {
        $this->_purchasedFactory = $purchasedFactory;
        $this->_itemsFactory = $itemsFactory;
        parent::__construct($coreString, $optionFactory, $coreData, $context, $data);
    }

    public function getLinks()
    {
        $this->_purchased = $this->_purchasedFactory->create()->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedItem = $this->_itemsFactory->create()->addFieldToFilter('order_item_id', $this->getItem()->getId());
        $this->_purchased->setPurchasedItems($purchasedItem);
        return $this->_purchased;
    }

    public function getLinksTitle()
    {
        if ($this->_purchased && $this->_purchased->getLinkSectionTitle()) {
            return $this->_purchased->getLinkSectionTitle();
        }
        return $this->_storeConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }
}
?>
