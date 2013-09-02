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
 * Enterprise checkout cart controller
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Controller_Cart
    extends Magento_Core_Controller_Front_Action
    implements Magento_Catalog_Controller_Product_View_Interface
{
    /**
     * Get checkout session model instance
     *
     * @return Magento_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session');
    }

    /**
     * Get customer session model instance
     *
     * @return Magento_Checkout_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }

    /**
     * Retrieve helper instance
     *
     * @return Magento_AdvancedCheckout_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->_objectManager->get('Magento_AdvancedCheckout_Helper_Data');
    }

    /**
     * Get cart model instance
     *
     * @return Magento_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Cart');
    }

    /**
     * Get failed items cart model instance
     *
     * @return Magento_AdvancedCheckout_Model_Cart
     */
    protected function _getFailedItemsCart()
    {
        return Mage::getSingleton('Magento_AdvancedCheckout_Model_Cart')
            ->setContext(Magento_AdvancedCheckout_Model_Cart::CONTEXT_FRONTEND);
    }

    /**
     * Add to cart products, which SKU specified in request
     *
     * @return void
     */
    public function advancedAddAction()
    {
        // check empty data
        /** @var $helper Magento_AdvancedCheckout_Helper_Data */
        $helper = $this->_objectManager->get('Magento_AdvancedCheckout_Helper_Data');
        $items = $this->getRequest()->getParam('items');
        foreach ($items as $k => $item) {
            if (empty($item['sku'])) {
                unset($items[$k]);
            }
        }
        if (empty($items) && !$helper->isSkuFileUploaded($this->getRequest())) {
            $this->_getSession()->addError($helper->getSkuEmptyDataMessageText());
            $this->_redirect('checkout/cart');
            return;
        }

        try {
            // perform data
            $cart = $this->_getFailedItemsCart()
                ->prepareAddProductsBySku($items)
                ->saveAffectedProducts();

            $this->_getSession()->addMessages($cart->getMessages());

            if ($cart->hasErrorMessage()) {
                Mage::throwException($cart->getErrorMessage());
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Add failed items to cart
     *
     * @return void
     */
    public function addFailedItemsAction()
    {
        $failedItemsCart = $this->_getFailedItemsCart()->removeAllAffectedItems();
        $failedItems = $this->getRequest()->getParam('failed', array());
        foreach ($failedItems as $data) {
            $data += array('sku' => '', 'qty' => '');
            $failedItemsCart->prepareAddProductBySku($data['sku'], $data['qty']);
        }
        $failedItemsCart->saveAffectedProducts();
        $this->_redirect('checkout/cart');
    }

    /**
     * Remove failed items from storage
     *
     * @return void
     */
    public function removeFailedAction()
    {
        $removed = $this->_getFailedItemsCart()->removeAffectedItem(
            $this->_objectManager->get('Magento_Core_Helper_Url')->urlDecode($this->getRequest()->getParam('sku'))
        );

        if ($removed) {
            $this->_getSession()->addSuccess(
                __('You removed the item.')
            );
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Remove all failed items from storage
     *
     * @return void
     */
    public function removeAllFailedAction()
    {
        $this->_getFailedItemsCart()->removeAllAffectedItems();
        $this->_getSession()->addSuccess(
            __('You removed the items.')
        );
        $this->_redirect('checkout/cart');
    }

    /**
     * Configure failed item options
     *
     * @return void
     */
    public function configureFailedAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $qty = $this->getRequest()->getParam('qty', 1);

        try {
            $params = new Magento_Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);

            $buyRequest = new Magento_Object(array(
                'product'   => $id,
                'qty'       => $qty
            ));

            $params->setBuyRequest($buyRequest);

            $this->_objectManager->get('Magento_Catalog_Helper_Product_View')->prepareAndRender($id, $this, $params);
        } catch (Magento_Core_Exception $e) {
            $this->_getCustomerSession()->addError($e->getMessage());
            $this->_redirect('*');
            return;
        } catch (Exception $e) {
            $this->_getCustomerSession()->addError(__('You cannot configure a product.'));
            Mage::logException($e);
            $this->_redirect('*');
            return;
        }
    }

    /**
     * Update failed items options data and add it to cart
     *
     * @return void
     */
    public function updateFailedItemOptionsAction()
    {
        $hasError = false;
        $id = (int)$this->getRequest()->getParam('id');
        $buyRequest = new Magento_Object($this->getRequest()->getParams());
        try {
            $cart = $this->_getCart();

            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($id);

            $cart->addProduct($product, $buyRequest)->save();

            $this->_getFailedItemsCart()->removeAffectedItem($this->getRequest()->getParam('sku'));

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $productName = $this->_objectManager
                        ->get('Magento_Core_Helper_Data')
                        ->escapeHtml($product->getName());
                    $message = __('You added %1 to your shopping cart.', $productName);
                    $this->_getSession()->addSuccess($message);
                }
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $hasError = true;
        } catch (Exception $e) {
            $this->_getSession()->addError(__('You cannot add a product.'));
            Mage::logException($e);
            $hasError = true;
        }

        if ($hasError) {
            $this->_redirect('checkout/cart/configureFailed', array('id' => $id, 'sku' => $buyRequest->getSku()));
        } else {
            $this->_redirect('checkout/cart');
        }
    }
}
