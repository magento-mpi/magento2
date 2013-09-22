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
namespace Magento\AdvancedCheckout\Controller;

class Cart
    extends \Magento\Core\Controller\Front\Action
    implements \Magento\Catalog\Controller\Product\View\ViewInterface
{
    /**
     * Get checkout session model instance
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session');
    }

    /**
     * Get customer session model instance
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCustomerSession()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session');
    }

    /**
     * Retrieve helper instance
     *
     * @return \Magento\AdvancedCheckout\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->_objectManager->get('Magento\AdvancedCheckout\Helper\Data');
    }

    /**
     * Get cart model instance
     *
     * @return \Magento\Checkout\Model\Cart
     */
    protected function _getCart()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Cart');
    }

    /**
     * Get failed items cart model instance
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    protected function _getFailedItemsCart()
    {
        return \Mage::getSingleton('Magento\AdvancedCheckout\Model\Cart')
            ->setContext(\Magento\AdvancedCheckout\Model\Cart::CONTEXT_FRONTEND);
    }

    /**
     * Add to cart products, which SKU specified in request
     *
     * @return void
     */
    public function advancedAddAction()
    {
        // check empty data
        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_objectManager->get('Magento\AdvancedCheckout\Helper\Data');
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
                throw new Magento_Core_Exception($cart->getErrorMessage());
            }
        } catch (\Magento\Core\Exception $e) {
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
            $this->_objectManager->get('Magento\Core\Helper\Url')->urlDecode($this->getRequest()->getParam('sku'))
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
            $params = new \Magento\Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);

            $buyRequest = new \Magento\Object(array(
                'product'   => $id,
                'qty'       => $qty
            ));

            $params->setBuyRequest($buyRequest);

            $this->_objectManager->get('Magento\Catalog\Helper\Product\View')->prepareAndRender($id, $this, $params);
        } catch (\Magento\Core\Exception $e) {
            $this->_getCustomerSession()->addError($e->getMessage());
            $this->_redirect('*');
            return;
        } catch (\Exception $e) {
            $this->_getCustomerSession()->addError(__('You cannot configure a product.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
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
        $buyRequest = new \Magento\Object($this->getRequest()->getParams());
        try {
            $cart = $this->_getCart();

            $product = $this->_objectManager->create('Magento_Catalog_Model_Product')
                ->setStoreId($this->_objectManager->get('Magento_Core_Model_StoreManager')->getStore()->getId())
                ->load($id);

            $cart->addProduct($product, $buyRequest)->save();

            $this->_getFailedItemsCart()->removeAffectedItem($this->getRequest()->getParam('sku'));

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $productName = $this->_objectManager
                        ->get('Magento\Core\Helper\Data')
                        ->escapeHtml($product->getName());
                    $message = __('You added %1 to your shopping cart.', $productName);
                    $this->_getSession()->addSuccess($message);
                }
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $hasError = true;
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('You cannot add a product.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $hasError = true;
        }

        if ($hasError) {
            $this->_redirect('checkout/cart/configureFailed', array('id' => $id, 'sku' => $buyRequest->getSku()));
        } else {
            $this->_redirect('checkout/cart');
        }
    }
}
