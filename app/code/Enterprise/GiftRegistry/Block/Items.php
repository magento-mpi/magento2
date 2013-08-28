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
 * Front end helper block to show giftregistry items
 */
class Enterprise_GiftRegistry_Block_Items extends Magento_Checkout_Block_Cart
{

    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        parent::__construct($catalogData, $coreData, $context, $data);
    }

    /**
     * Return list of gift registry items
     *
     * @return array
     */
    public function getItems()
    {
        if (!$this->hasItemCollection()) {
            if (!$this->getEntity()) {
                return array();
            }
            $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Item')->getCollection()
                ->addRegistryFilter($this->getEntity()->getId());

            $quoteItemsCollection = array();
            $quote = Mage::getModel('Magento_Sales_Model_Quote')->setItemCount(true);
            $emptyQuoteItem = Mage::getModel('Magento_Sales_Model_Quote_Item');
            foreach ($collection as $item) {
                $product = $item->getProduct();
                $remainingQty = $item->getQty() - $item->getQtyFulfilled();
                if ($remainingQty < 0) {
                    $remainingQty = 0;
                }
                // Create a new qoute item and import data from gift registry item to it
                $quoteItem = clone $emptyQuoteItem;
                $quoteItem->addData($item->getData())
                    ->setQuote($quote)
                    ->setProduct($product)
                    ->setRemainingQty($remainingQty)
                    ->setOptions($item->getOptions());

                $product->setCustomOptions($item->getOptionsByCode());
                if ($this->_catalogData->canApplyMsrp($product)) {
                    $quoteItem->setCanApplyMsrp(true);
                    $product->setRealPriceHtml(
                        Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(
                            $this->_taxData->getPrice($product, $product->getFinalPrice(), true)
                        ))
                    );
                    $product->setAddToCartUrl($this->helper('Magento_Checkout_Helper_Cart')->getAddUrl($product));
                } else {
                    $quoteItem->setGiftRegistryPrice($product->getFinalPrice());
                    $quoteItem->setCanApplyMsrp(false);
                }

                $quoteItemsCollection[] = $quoteItem;
            }

            $this->setData('item_collection', $quoteItemsCollection);
        }
        return $this->_getData('item_collection');
    }

    /**
     * Return current gift registry entity
     *
     * @return Enterprise_GiftRegistry_Model_Resource_Item_Collection
     */
    public function getEntity()
    {
         if (!$this->hasEntity()) {
            $this->setData('entity', Mage::registry('current_entity'));
        }
        return $this->_getData('entity');
    }

    /**
     * Return "add to cart" url
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/addToCart', array('_current' => true));
    }

    /**
     * Return update action form url
     *
     * @return string
     */
    public function getActionUpdateUrl()
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

}
