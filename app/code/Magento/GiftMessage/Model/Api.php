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
namespace Magento\GiftMessage\Model;

class Api extends \Magento\Checkout\Model\Api\Resource\Product
{
    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Config\Scope
     */
    protected $_configScope;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Scope $configScope
     * @param \Magento\Api\Helper\Data $apiHelper
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Scope $configScope,
        \Magento\Api\Helper\Data $apiHelper
    ) {
        $this->_configScope = $configScope;
        $this->_eventManager = $eventManager;
        parent::__construct($apiHelper);
    }

    /**
     * Return an Array of attributes.
     *
     * @param Array $arr
     * @return array
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
     * @param string $entityId
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Sales\Model\Quote $quote
     * @return array
     */
    protected function _setGiftMessage($entityId, $request, $quote)
    {
        $currentScope = $this->_configScope->getCurrentScope();

        /**
         * Below code will catch exceptions only in DeveloperMode
         */
        try {
            /** Frontend area events must be loaded as we emulate frontend behavior. */
            $this->_configScope->setCurrentScope(\Magento\Core\Model\App\Area::AREA_FRONTEND);
            $this->_eventManager->dispatch(
                'checkout_controller_onepage_save_shipping_method',
                array('request' => $request, 'quote' => $quote)
            );
            /** Restore config scope */
            $this->_configScope->setCurrentScope($currentScope);
            return array('entityId' => $entityId, 'result' => true, 'error' => '');
        } catch (\Exception $e) {
            /** Restore config scope */
            $this->_configScope->setCurrentScope($currentScope);
            return array('entityId' => $entityId, 'result' => false, 'error' => $e->getMessage());
        }
    }

    /**
     * Set GiftMessage for a Quote.
     *
     * @param string $quoteId
     * @param array $giftMessage
     * @param string $store
     * @return array
     */
    public function setForQuote($quoteId, $giftMessage, $store = null)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->_getQuote($quoteId, $store);

        $giftMessage = $this->_prepareData($giftMessage);
        if (empty($giftMessage)) {
            $this->_fault('giftmessage_invalid_data');
        }

        $giftMessage['type'] = 'quote';
        $giftMessages = array($quoteId => $giftMessage);
        $request = new \Magento\Core\Controller\Request\Http();
        $request->setParam("giftmessage", $giftMessages);

        return $this->_setGiftMessage($quote->getId(), $request, $quote);
    }

    /**
     * Set a GiftMessage to QuoteItem by product
     *
     * @param string $quoteId
     * @param array $productsAndMessages
     * @param string $store
     * @return array
     */
    public function setForQuoteProduct($quoteId, $productsAndMessages, $store = null)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
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
     * @param string $quoteItemId
     * @param array $giftMessage
     * @param string $store
     * @return array
     */
    public function setForQuoteItem($quoteItemId, $giftMessage, $store = null)
    {
        /** @var $quote \Magento\Sales\Model\Quote\Item */
        $quoteItem = \Mage::getModel('\Magento\Sales\Model\Quote\Item')->load($quoteItemId);
        if (is_null($quoteItem->getId())) {
            $this->_fault("quote_item_not_exists");
        }

        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->_getQuote($quoteItem->getQuoteId(), $store);

        $giftMessage = $this->_prepareData($giftMessage);
        $giftMessage['type'] = 'quote_item';

        $giftMessages = array($quoteItem->getId() => $giftMessage);

        $request = new \Magento\Core\Controller\Request\Http();
        $request->setParam("giftmessage", $giftMessages);

        return $this->_setGiftMessage($quoteItemId, $request, $quote);
    }
}
