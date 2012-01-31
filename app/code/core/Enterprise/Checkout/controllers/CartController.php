<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise checkout cart controller
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_CartController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }

    /**
     * Get customer session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session');
    }

    /**
     * Retrieve helper instance
     *
     * @return Enterprise_Checkout_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Enterprise_Checkout_Helper_Data');
    }

    /**
     * Add to cart products, which SKU specified in request
     *
     * @return void
     */
    public function advancedAddAction()
    {
        try {
            /** @var $cart Enterprise_Checkout_Model_Cart */
            $cart = Mage::getModel('Enterprise_Checkout_Model_Cart');
            $cart->prepareAddProductsBySku($this->getRequest()->getParam('items'));
            $cart->saveAffectedProducts();
            $this->_getSession()->addMessages($cart->getMessages());
            $cart->removeSuccessItems();

            if ($cart->hasErrorMessage()) {
                $this->_getSession()->addError(
                    $cart->getErrorMessage()
                );
            }
        } catch (Enterprise_Checkout_Exception $e) {
            $this->_getSession()->addError(
                $e->getMessage()
            );
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
        $failedItems = $this->getRequest()->getParam('failed', array());

        /** @var $cart Mage_Checkout_Model_Cart */
        $cart = Mage::getSingleton('Mage_Checkout_Model_Cart');

        /** @var $failedItemsCart Enterprise_Checkout_Model_Cart */
        $failedItemsCart = Mage::getModel('Enterprise_Checkout_Model_Cart');
        $failedItemsCart->removeAllAffectedItems();

        foreach ($failedItems as $data) {
            if (!isset($data['qty']) || !isset($data['sku'])) {
                continue;
            }
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
        $removed = Mage::getModel('Enterprise_Checkout_Model_Cart')->removeAffectedItem(
            Mage::helper('Mage_Core_Helper_Url')->urlDecode($this->getRequest()->getParam('sku'))
        );

        if ($removed) {
            $this->_getSession()->addSuccess(
                $this->__('Item was successfully removed.')
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
        Mage::getModel('Enterprise_Checkout_Model_Cart')->removeAllAffectedItems();
        $this->_getSession()->addSuccess(
            $this->__('Items were successfully removed.')
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
        $id = (int) $this->getRequest()->getParam('id');
        $qty = $this->getRequest()->getParam('qty', 1);

        try {
            $params = new Varien_Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);

            $buyRequest = new Varien_Object(array(
                'product'   => $id,
                'qty'       => $qty
            ));

            $params->setBuyRequest($buyRequest);

            Mage::helper('Mage_Catalog_Helper_Product_View')->prepareAndRender($id, $this, $params);
        } catch (Mage_Core_Exception $e) {
            $this->_getCustomerSession()->addError($e->getMessage());
            $this->_redirect('*');
            return;
        } catch (Exception $e) {
            $this->_getCustomerSession()->addError($this->__('Cannot configure product'));
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
        try {
            $id = (int) $this->getRequest()->getParam('id');
            $buyRequest = new Varien_Object($this->getRequest()->getParams());

            /** @var $cart Mage_Checkout_Model_Cart */
            $cart = Mage::getSingleton('Mage_Checkout_Model_Cart');

            $product = Mage::getModel('Mage_Catalog_Model_Product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($id);

            $cart->addProduct($product, $buyRequest);
            $cart->save();

            Mage::getModel('Enterprise_Checkout_Model_Cart')->removeAffectedItem(
                $this->getRequest()->getParam('sku')
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $productName = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($product->getName());
                    $message = $this->__('%s was added to your shopping cart.', $productName);
                    $this->_getSession()->addSuccess($message);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot add product'));
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }
}
