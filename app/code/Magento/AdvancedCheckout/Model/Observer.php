<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Checkout processing model
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
namespace Magento\AdvancedCheckout\Model;

class Observer
{
    /**
     * Checkout data
     *
     * @var Magento_AdvancedCheckout_Helper_Data
     */
    protected $_checkoutData = null;

    /**
     * @param Magento_AdvancedCheckout_Helper_Data $checkoutData
     */
    public function __construct(
        Magento_AdvancedCheckout_Helper_Data $checkoutData
    ) {
        $this->_checkoutData = $checkoutData;
    }

    /**
     * Get cart model instance
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    protected function _getCart()
    {
        return \Mage::getSingleton('Magento\AdvancedCheckout\Model\Cart');
    }

    /**
     * Returns cart model for backend
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    protected function _getBackendCart(\Magento\Event\Observer $observer)
    {
        $storeId = $observer->getRequestModel()->getParam('storeId');
        if (is_null($storeId)) {
            $storeId = $observer->getRequestModel()->getParam('store_id');
        }
        return $this->_getCart()
            ->setSession($observer->getSession())
            ->setContext(\Magento\AdvancedCheckout\Model\Cart::CONTEXT_ADMIN_ORDER)
            ->setCurrentStore((int)$storeId);
    }

    /**
     * Check submitted SKU's form the form or from error grid
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function addBySku(\Magento\Event\Observer $observer)
    {
        /* @var $request \Magento\Core\Controller\Request\Http */
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

        $addBySkuItems = $request->getPost(\Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE, array());
        $items = $request->getPost('item', array());
        if (!$addBySkuItems) {
            return;
        }
        foreach ($addBySkuItems as $id => $params) {
            $sku = isset($params['sku']) ? $params['sku'] : $id;
            $cart->prepareAddProductBySku($sku, $params['qty'], isset($items[$id]) ? $items[$id] : array());
        }
        /* @var $orderCreateModel \Magento\Adminhtml\Model\Sales\Order\Create */
        $orderCreateModel = $observer->getOrderCreateModel();
        $cart->saveAffectedProducts($orderCreateModel, false);
        // We have already saved succeeded add by SKU items in saveAffectedItems(). This prevents from duplicate saving.
        $request->setPost('item', array());
    }

    /**
     * Upload and parse CSV file with SKUs
     *
     * @param \Magento\Event\Observer $observer
     * @return null
     */
    public function uploadSkuCsv(\Magento\Event\Observer $observer)
    {
        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_checkoutData;
        $rows = $helper->isSkuFileUploaded($observer->getRequestModel())
            ? $helper->processSkuFileUploading($observer->getSession())
            : array();
        if (empty($rows)) {
            return;
        }

        /* @var $orderCreateModel \Magento\Adminhtml\Model\Sales\Order\Create */
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
        $address = \Mage::getModel('Magento\Sales\Model\Quote\Address');
        $address->setData($realAddress->getData());
        $address
            ->setId(null)
            ->unsEntityId()
            ->unsetData('cached_items_nominal')
            ->unsetData('cached_items_nonnominal')
            ->unsetData('cached_items_all')
            ->setQuote($quote);
        return $address;
    }

    /**
     * Calculate failed items quote-related data
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function collectTotalsFailedItems($observer)
    {
        if ($observer->getEvent()->getAction()->getFullActionName() != 'checkout_cart_index') {
            return;
        }

        /** @var $realQuote \Magento\Sales\Model\Quote */
        $realQuote = \Mage::getSingleton('Magento\Sales\Model\Quote');
        $affectedItems = $this->_getCart()->getFailedItems();
        if (empty($affectedItems)) {
            return;
        }

        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = \Mage::getModel('Magento\Sales\Model\Quote');
        $collection = new \Magento\Data\Collection();

        foreach ($this->_checkoutData->getFailedItems(false) as $item) {
            /** @var $item \Magento\Sales\Model\Quote\Item */
            if ((float)$item->getQty() <= 0) {
                $item->setSkuRequestedQty($item->getQty());
                $item->setData('qty', 1);
            }
            $item->setQuote($quote);
            $collection->addItem($item);
        }

        $quote->preventSaving()->setItemsCollection($collection);

        $quote->setShippingAddress($this->_copyAddress($quote, $realQuote->getShippingAddress()));
        $quote->setBillingAddress($this->_copyAddress($quote, $realQuote->getBillingAddress()));
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
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function addCartLink($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!$block instanceof \Magento\Checkout\Block\Cart\Sidebar) {
            return;
        }

        $failedItemsCount = count(\Mage::getSingleton('Magento\AdvancedCheckout\Model\Cart')->getFailedItems());
        if ($failedItemsCount > 0) {
            $block->setAllowCartLink(true);
            $block->setCartEmptyMessage(__('%1 item(s) need your attention.', $failedItemsCount));
        }
    }
}
