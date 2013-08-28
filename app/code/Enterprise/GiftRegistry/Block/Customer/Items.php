<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer gift registry view items block
 */
class Enterprise_GiftRegistry_Block_Customer_Items extends Magento_Catalog_Block_Product_Abstract
{

    /**
     * @param array $data
     * @param Magento_Checkout_Helper_Cart $checkoutCart
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Catalog_Helper_Product_Compare $catalogProductCompare
     * @param Magento_Page_Helper_Layout $pageLayout
     * @param Magento_Catalog_Helper_Image $catalogImage
     * @param  $taxData
     * @param  $catalogData
     * @param  $coreData
     * @param  $context
     * @param  $data
     */
    public function __construct(
        array $data,
        Magento_Checkout_Helper_Cart $checkoutCart,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Catalog_Helper_Product_Compare $catalogProductCompare,
        Magento_Page_Helper_Layout $pageLayout,
        Magento_Catalog_Helper_Image $catalogImage,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($catalogImage, $pageLayout, $catalogProductCompare, $wishlistData, $checkoutCart, $data, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Return gift registry form header
     */
    public function getFormHeader()
    {
        return __('View Gift Registry %1', $this->getEntity()->getTitle());
    }

    /**
     * Return list of gift registries
     *
     * @return Enterprise_GiftRegistry_Model_Resource_Item_Collection
     */
    public function getItemCollection()
    {
         if (!$this->hasItemCollection()) {
             $attributes = Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes();
             $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Item')->getCollection()
                ->addRegistryFilter($this->getEntity()->getId());
            $this->setData('item_collection', $collection);
        }
        return $this->_getData('item_collection');
    }

    /**
     * Retrieve item formatted date
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        return $this->formatDate($item->getAddedAt(), Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Retrieve escaped item note
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getEscapedNote($item)
    {
        return $this->escapeHtml($item->getData('note'));
    }

    /**
     * Retrieve item qty
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getItemQty($item)
    {
        return $item->getQty()*1;
    }

    /**
     * Retrieve item fulfilled qty
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getItemQtyFulfilled($item)
    {
        return $item->getQtyFulfilled()*1;
    }

    /**
     * Return action form url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/updateItems', array('_current' => true));
    }

    /**
     * Return back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('giftregistry');
    }

    /**
     * Return back url to search result page
     *
     * @return string
     */
    public function getSearchBackUrl()
    {
        return $this->getUrl('*/search/results');
    }

    /**
     * Returns product price
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return mixed
     */
    public function getPrice($item)
    {
        $product = $item->getProduct();
        $product->setCustomOptions($item->getOptionsByCode());
        return $this->_coreData->currency($product->getFinalPrice(),true,true);
    }
}
