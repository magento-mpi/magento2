<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model;

/**
 * Admin Checkout processing model
 *
 */
class Observer
{
    /**
     * Checkout data
     *
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote;

    /**
     * @param \Magento\Sales\Model\Quote $quote
     * @param Cart $cart
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Sales\Model\Quote\AddressFactory $addressFactory
     */
    public function __construct(
        \Magento\Sales\Model\Quote $quote,
        Cart $cart,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Quote\AddressFactory $addressFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_quote = $quote;
        $this->_cart = $cart;
        $this->_checkoutData = $checkoutData;
        $this->_quoteFactory = $quoteFactory;
        $this->_addressFactory = $addressFactory;
    }

    /**
     * Returns cart model for backend
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return Cart
     */
    protected function _getBackendCart(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $observer->getRequestModel()->getParam('storeId');
        if (is_null($storeId) || $storeId === '') {
            $storeId = $observer->getRequestModel()->getParam('store_id');

            if (is_null($storeId) || $storeId === '') {
                $storeId = $observer->getSession()->getStoreId();
            }
        }
        return $this->_cart->setSession(
            $observer->getSession()
        )->setContext(
            Cart::CONTEXT_ADMIN_ORDER
        )->setCurrentStore(
            (int)$storeId
        );
    }

    /**
     * Check submitted SKU's form the form or from error grid
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addBySku(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $request \Magento\Framework\App\RequestInterface */
        $request = $observer->getRequestModel();
        $cart = $this->_getBackendCart($observer);

        if (empty($request) || empty($cart)) {
            return;
        }

        $removeFailed = $request->getPost('sku_remove_failed');

        if ($removeFailed || $request->getPost('from_error_grid')) {
            $cart->removeAllAffectedItems();
            if ($removeFailed) {
                return;
            }
        }

        $sku = $observer->getRequestModel()->getPost('remove_sku', false);

        if ($sku) {
            $this->_getBackendCart($observer)->removeAffectedItem($sku);
            return;
        }

        $addBySkuItems = $request->getPost(
            \Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE,
            array()
        );
        $items = $request->getPost('item', array());
        if (!$addBySkuItems) {
            return;
        }
        foreach ($addBySkuItems as $id => $params) {
            $sku = (string) (isset($params['sku']) ? $params['sku'] : $id);
            $cart->prepareAddProductBySku($sku, $params['qty'], isset($items[$id]) ? $items[$id] : array());
        }
        /* @var $orderCreateModel \Magento\Sales\Model\AdminOrder\Create */
        $orderCreateModel = $observer->getOrderCreateModel();
        $cart->saveAffectedProducts($orderCreateModel, false);
        // We have already saved succeeded add by SKU items in saveAffectedItems(). This prevents from duplicate saving.
        $request->setPost('item', array());
    }

    /**
     * Upload and parse CSV file with SKUs
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function uploadSkuCsv(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_checkoutData;
        $rows = $helper->isSkuFileUploaded(
            $observer->getRequestModel()
        ) ? $helper->processSkuFileUploading() : array();
        if (empty($rows)) {
            return;
        }

        /* @var $orderCreateModel \Magento\Sales\Model\AdminOrder\Create */
        $orderCreateModel = $observer->getOrderCreateModel();
        $cart = $this->_getBackendCart($observer);
        $cart->prepareAddProductsBySku($rows);
        $cart->saveAffectedProducts($orderCreateModel, false);
    }

    /**
     * Copy real address to the quote
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Sales\Model\Quote\Address $realAddress
     * @return \Magento\Sales\Model\Quote\Address
     */
    protected function _copyAddress($quote, $realAddress)
    {
        $address = $this->_addressFactory->create();
        $address->setData($realAddress->getData());
        $address->setId(
            null
        )->unsEntityId()->unsetData(
            'cached_items_nominal'
        )->unsetData(
            'cached_items_nonnominal'
        )->unsetData(
            'cached_items_all'
        )->setQuote(
            $quote
        );
        return $address;
    }

    /**
     * Calculate failed items quote-related data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function collectTotalsFailedItems($observer)
    {
        if ($observer->getEvent()->getFullActionName() != 'checkout_cart_index') {
            return;
        }

        $affectedItems = $this->_cart->getFailedItems();
        if (empty($affectedItems)) {
            return;
        }

        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->_quoteFactory->create();
        $collection = $this->_collectionFactory->create();

        foreach ($this->_checkoutData->getFailedItems(false) as $item) {
            /** @var $item \Magento\Sales\Model\Quote\Item */
            if ((double)$item->getQty() <= 0) {
                $item->setSkuRequestedQty($item->getQty());
                $item->setData('qty', 1);
            }
            $item->setQuote($quote);
            $collection->addItem($item);
        }

        $quote->preventSaving()->setItemsCollection($collection);

        $quote->setShippingAddress($this->_copyAddress($quote, $this->_quote->getShippingAddress()));
        $quote->setBillingAddress($this->_copyAddress($quote, $this->_quote->getBillingAddress()));
        $quote->setTotalsCollectedFlag(false)->collectTotals();

        foreach ($quote->getAllItems() as $item) {
            /** @var $item \Magento\Sales\Model\Quote\Item */
            if ($item->hasSkuRequestedQty()) {
                $item->setData('qty', $item->getSkuRequestedQty());
            }
        }
    }

    /**
     * Add link to cart in cart sidebar to view grid with failed products
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addCartLink($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!$block instanceof \Magento\Checkout\Block\Cart\Sidebar) {
            return;
        }

        $failedItemsCount = count($this->_cart->getFailedItems());
        if ($failedItemsCount > 0) {
            $block->setAllowCartLink(true);
            $block->setCartEmptyMessage(__('%1 item(s) need your attention.', $failedItemsCount));
        }
    }
}
