<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Admin Checkout processing model
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Model_Observer
{
    /**
     * Get cart model instance
     *
     * @return Enterprise_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('enterprise_checkout/cart');
    }

    /**
     * Check submitted SKU's form the form or from error grid
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function addBySku(Varien_Event_Observer $observer)
    {
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $observer->getRequestModel();
        /* @var $cart Enterprise_Checkout_Model_Cart */
        $cart = $this->_getCart()->setSession($observer->getSession());
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
        $addBySkuItems = $request->getPost(Enterprise_Checkout_Block_Adminhtml_Sku_Abstract::LIST_TYPE, array());
        if (!$addBySkuItems) {
            return;
        }
        foreach ($addBySkuItems as $id => $params) {
            $sku = isset($params['sku']) ? $params['sku'] : $id;
            $cart->prepareAddProductBySku($sku, $params['qty'], isset($items[$id]) ? $items[$id] : array());
        }
        /* @var $orderCreateModel Mage_Adminhtml_Model_Sales_Order_Create */
        $orderCreateModel = $observer->getOrderCreateModel();
        $cart->saveAffectedProducts($orderCreateModel);
        $cart->removeSuccessItems();
    }

    /**
     * Upload and parse CSV file with SKUs
     *
     * @param Varien_Event_Observer $observer
     */
    public function uploadSkuCsv(Varien_Event_Observer $observer)
    {
        /* @var $importModel Enterprise_Checkout_Model_Import */
        $importModel = Mage::getModel('enterprise_checkout/import');
        if ($importModel->uploadFile()) {
            /* @var $orderCreateModel Mage_Adminhtml_Model_Sales_Order_Create */
            $orderCreateModel = $observer->getOrderCreateModel();
            try {
                $cart = $this->_getCart()->setSession($observer->getSession());
                $cart->prepareAddProductsBySku($importModel->getDataFromCsv());
                $cart->saveAffectedProducts($orderCreateModel);
            }
            catch (Mage_Core_Exception $e) {
                $observer->getSession()->addError($e->getMessage());
            }
        }
    }

    /**
     * Create handle for sku failed products
     *
     * @param Varien_Event_Observer $observer
     */
    public function createSkuErrorHandleLayout(Varien_Event_Observer $observer)
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();

        $affectedItems = $this->_getCart()->getFailedItems();
        if (!empty($affectedItems)) {
            $layout->getUpdate()->addHandle(Enterprise_Checkout_Helper_Data::SKU_FAILED_PRODUCTS_HANDLE);
        }
    }

    /**
     * Copy real address to the quote
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Mage_Sales_Model_Quote_Address $realAddress
     * @return Mage_Sales_Model_Quote_Address
     */
    protected function _copyAddress($quote, $realAddress)
    {
        $address = Mage::getModel('sales/quote_address');
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
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function collectTotalsFailedItems($observer)
    {
        if ($observer->getEvent()->getAction()->getFullActionName() != 'checkout_cart_index') {
            return;
        }

        /** @var $realQuote Mage_Sales_Model_Quote */
        $realQuote = Mage::getSingleton('sales/quote');
        $affectedItems = $this->_getCart()->getFailedItems();
        if (empty($affectedItems)) {
            return;
        }

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote');
        $collection = new Varien_Data_Collection();

        foreach (Mage::helper('enterprise_checkout')->getFailedItems(true) as $item) {
            $item->setQuote($quote);
            $collection->addItem($item);
        }

        $quote->preventSaving()->setItemsCollection($collection);
        $quote->setShippingAddress($this->_copyAddress($quote, $realQuote->getShippingAddress()));
        $quote->setBillingAddress($this->_copyAddress($quote, $realQuote->getBillingAddress()));
        $quote->setTotalsCollectedFlag(false)->collectTotals();
    }

    /**
     * Add link to cart in cart sidebar to view grid with failed products
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function addCartLink($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!$block instanceof Mage_Checkout_Block_Cart_Sidebar) {
            return;
        }

        $failedItemsCount = count(Mage::getSingleton('enterprise_checkout/cart')->getFailedItems());
        if ($failedItemsCount > 0) {
            $block->setAllowCartLink(true);
            $block->setCartEmptyMessage(Mage::helper('enterprise_checkout')->__('You have %d item(s) requiring attention.', $failedItemsCount));
        }
    }
}
