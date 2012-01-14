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
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get customer session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve helper instance
     *
     * @return Enterprise_Checkout_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('enterprise_checkout');
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
            $cart = Mage::getModel('enterprise_checkout/cart');
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
        $cart = Mage::getSingleton('checkout/cart');

        /** @var $failedItemsCart Enterprise_Checkout_Model_Cart */
        $failedItemsCart = Mage::getModel('enterprise_checkout/cart');

        foreach ($failedItems as $productId => $data) {
            if (!isset($data['qty']) || !isset($data['sku'])) {
                continue;
            }
            $checkedItem = $failedItemsCart->checkItem($data['sku'], $data['qty']);
            if ($checkedItem['code'] == Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_SUCCESS) {
                $cart->addProduct($productId, $data['qty']);
                $failedItemsCart->removeAffectedItem($data['sku']);
            } else {
                $failedItemsCart->updateItemQty($data['sku'], $data['qty']);
            }
        }
        $cart->save();
        $this->_redirect('checkout/cart');
    }

    /**
     * Remove failed items from storage
     *
     * @return void
     */
    public function removeFailedAction()
    {
        $removed = Mage::getModel('enterprise_checkout/cart')->removeAffectedItem(
            Mage::helper('core/url')->urlDecode($this->getRequest()->getParam('sku'))
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
        Mage::getModel('enterprise_checkout/cart')->removeAllAffectedItems();
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

            Mage::helper('catalog/product_view')->prepareAndRender($id, $this, $params);
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
            $cart = Mage::getSingleton('checkout/cart');
            $cart->addProduct($id, $buyRequest);
            $cart->save();
            Mage::getModel('enterprise_checkout/cart')->removeAffectedItem(
                $this->getRequest()->getParam('sku')
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot add product'));
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }
}
