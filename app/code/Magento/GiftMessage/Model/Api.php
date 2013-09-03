<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GiftMessage api
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Model_Api extends Magento_Checkout_Model_Api_Resource_Product
{
    /**
     * @var Magento_Core_Controller_Request_HttpFactory
     */
    protected $_coreRequestHttpFactory;

    /**
     * @param Magento_Core_Controller_Request_HttpFactory $coreRequestHttpFactory
     * @param Magento_Catalog_Helper_Product $catalogProduct
     * @param Magento_Api_Helper_Data $apiHelper
     */
    public function __construct(
        Magento_Core_Controller_Request_HttpFactory $coreRequestHttpFactory,
        Magento_Catalog_Helper_Product $catalogProduct,
        Magento_Api_Helper_Data $apiHelper
    ) {
        $this->_coreRequestHttpFactory = $coreRequestHttpFactory;
        parent::__construct($catalogProduct, $apiHelper);
    }

    /**
     * Return an Array of attributes.
     *
     * @param Array $obj
     * @return Array
     */
    protected function _prepareData($arr)
    {
        if (is_array($arr)) {
            return $arr;
        }
        return array();
    }

    /**
     * Raise event for setting a giftMessage.
     *
     * @param String $entityId
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Sales_Model_Quote $quote
     * @return AssociativeArray
     */
    protected function _setGiftMessage($entityId, $request, $quote)
    {

        /**
         * Below code will catch exceptions only in DeveloperMode
         * @see Magento_Core_Model_App::_callObserverMethod($object, $method, $observer)
         * And result of Mage::dispatchEvent will always return an Object of Magento_Core_Model_App.
         */
        try {
            /** Frontend area events must be loaded as we emulate frontend behavior. */
            Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_FRONTEND, Magento_Core_Model_App_Area::PART_EVENTS);
            Mage::dispatchEvent(
                'checkout_controller_onepage_save_shipping_method',
                array('request' => $request, 'quote' => $quote)
            );
            return array('entityId' => $entityId, 'result' => true, 'error' => '');
        } catch (Exception $e) {
            return array('entityId' => $entityId, 'result' => false, 'error' => $e->getMessage());
        }
    }

    /**
     * Set GiftMessage for a Quote.
     *
     * @param String $quoteId
     * @param AssociativeArray $giftMessage
     * @param String $store
     * @return AssociativeArray
     */
    public function setForQuote($quoteId, $giftMessage, $store = null)
    {
        /** @var $quote Magento_Sales_Model_Quote */
        $quote = $this->_getQuote($quoteId, $store);

        $giftMessage = $this->_prepareData($giftMessage);
        if (empty($giftMessage)) {
            $this->_fault('giftmessage_invalid_data');
        }

        $giftMessage['type'] = 'quote';
        $giftMessages = array($quoteId => $giftMessage);
        $request = $this->_coreRequestHttpFactory->create();
        $request->setParam("giftmessage", $giftMessages);

        return $this->_setGiftMessage($quote->getId(), $request, $quote);
    }

    /**
     * Set a GiftMessage to QuoteItem by product
     *
     * @param String $quoteId
     * @param Array $productsAndMessages
     * @param String $store
     * @return array
     */
    public function setForQuoteProduct($quoteId, $productsAndMessages, $store = null)
    {
        /** @var $quote Magento_Sales_Model_Quote */
        $quote = $this->_getQuote($quoteId, $store);

        $productsAndMessages = $this->_prepareData($productsAndMessages);
        if (empty($productsAndMessages)) {
            $this->_fault('invalid_data');
        }

        if (count($productsAndMessages) == 2
                && isset($productsAndMessages['product'])
                && isset($productsAndMessages['message'])) {
            $productsAndMessages = array($productsAndMessages);
        }

        $results = array();
        foreach ($productsAndMessages as $productAndMessage) {
            if (isset($productAndMessage['product']) && isset($productAndMessage['message'])) {
                $product = $this->_prepareData($productAndMessage['product']);
                if (empty($product)) {
                    $this->_fault('product_invalid_data');
                }
                $message = $this->_prepareData($productAndMessage['message']);
                if (empty($message)) {
                    $this->_fault('giftmessage_invalid_data');
                }

                if (isset($product['product_id'])) {
                    $productByItem = $this->_getProduct($product['product_id'], $store, "id");
                } elseif (isset($product['sku'])) {
                    $productByItem = $this->_getProduct($product['sku'], $store, "sku");
                } else {
                    continue;
                }

                $productObj = $this->_getProductRequest($product);
                $quoteItem = $this->_getQuoteItemByProduct($quote, $productByItem, $productObj);
                $results[] = $this->setForQuoteItem($quoteItem->getId(), $message, $store);
            }
        }

        return $results;
    }

    /**
     * Set GiftMessage for a QuoteItem by its Id.
     *
     * @param String $quoteItemId
     * @param AssociativeArray $giftMessage
     * @param String $store
     * @return AssociativeArray
     */
    public function setForQuoteItem($quoteItemId, $giftMessage, $store = null)
    {
        /** @var $quote Magento_Sales_Model_Quote_Item */
        $quoteItem = Mage::getModel('Magento_Sales_Model_Quote_Item')->load($quoteItemId);
        if (is_null($quoteItem->getId())) {
            $this->_fault("quote_item_not_exists");
        }

        /** @var $quote Magento_Sales_Model_Quote */
        $quote = $this->_getQuote($quoteItem->getQuoteId(), $store);

        $giftMessage = $this->_prepareData($giftMessage);
        $giftMessage['type'] = 'quote_item';

        $giftMessages = array($quoteItem->getId() => $giftMessage);

        $request = $this->_coreRequestHttpFactory->create();
        $request->setParam("giftmessage", $giftMessages);

        return $this->_setGiftMessage($quoteItemId, $request, $quote);
    }
}
