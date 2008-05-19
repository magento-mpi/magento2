<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart controller
 */
class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack()
    {
        if (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()) {

            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                Mage::getSingleton('checkout/session')->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }

    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    /**
     * Shopping cart display action
     */
    public function indexAction()
    {
        /**
         * Cache is not used for cart page
         */
        $this->_getQuote()->setCacheKey(false);
        $cart = $this->_getCart();

        $cart->init();
        $cart->save();

        if (!$this->_getQuote()->validateMinimumAmount()) {
            $warning = Mage::getStoreConfig('sales/minimum_order/description');
            $cart->getCheckoutSession()->addNotice($warning);
        }

        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                $cart->getCheckoutSession()->addMessage($message);
            }
        }

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    /**
     * Add product to shopping cart action
     */
    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        $product= $this->_initProduct();

        /**
         * Check product availability
         */
        if (!$product) {
            $this->_goBack();
            return;
        }

        try {
            $cart->addProduct($product, $params);
            $cart->save();

            /**
             * @todo add related products
             */

            Mage::dispatchEvent('checkout_cart_add_product', array('product'=>$product));

            $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
            if (!Mage::getSingleton('checkout/session')->getNoCartRedirect(true)) {
                Mage::getSingleton('checkout/session')->addSuccess($message);
                $this->_goBack();
            }
        }
        catch (Mage_Core_Exception $e) {
            if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
            } else {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
            }

            $url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
            $this->_goBack();
        }
    }

    public function addgroupAction()
    {
        $productIds = $this->getRequest()->getParam('products');
        if (is_array($productIds)) {
            $cart = $this->_getCart();
            $cart->addProductsByIds($productIds);
            $cart->save();
        }
        $this->_goBack();
    }

    /**
     * Adding product to shopping cart action
     */
    public function _addAction()
    {
//        $productId       = (int) $this->getRequest()->getParam('product');
//        $qty             = (float) $this->getRequest()->getParam('qty', 1);
//        $relatedProducts = (string) $this->getRequest()->getParam('related_product');
//
//        if (!$productId) {
//            $this->_goBack();
//            return;
//        }
//
//        $additionalIds = array();
//        /**
//         * Parse related products
//         */
//        if ($relatedProducts) {
//            $relatedProducts = explode(',', $relatedProducts);
//            if (is_array($relatedProducts)) {
//                foreach ($relatedProducts as $relatedId) {
//                    $additionalIds[] = $relatedId;
//                }
//            }
//        }

        try {
//            Varien_Profiler::start(__METHOD__ . '/getCart');
//            $cart = $this->_getCart();
//            Varien_Profiler::stop(__METHOD__ . '/getCart');

//            Varien_Profiler::start(__METHOD__ . '/loadProduct');
//            $product = Mage::getModel('catalog/product')
//                ->setStoreId(Mage::app()->getStore()->getId())
//                ->load($productId)
//                ->setConfiguredAttributes($this->getRequest()->getParam('super_attribute'))
//                ->setGroupedProducts($this->getRequest()->getParam('super_group', array()));
//            Varien_Profiler::stop(__METHOD__ . '/loadProduct');
//
//            $eventArgs = array(
//                'product' => $product,
//                'qty' => $qty,
//                'additional_ids' => $additionalIds,
//                'request' => $this->getRequest(),
//                'response' => $this->getResponse(),
//            );

            Mage::dispatchEvent('checkout_cart_before_add', $eventArgs);
            Varien_Profiler::start(__METHOD__ . '/addProduct');
            $cart->addProduct($product, $qty)
                ->addProductsByIds($additionalIds);
            Varien_Profiler::stop(__METHOD__ . '/addProduct');

            Mage::dispatchEvent('checkout_cart_after_add', $eventArgs);

            Varien_Profiler::start(__METHOD__ . '/saveCart');
            $cart->save();
            Varien_Profiler::stop(__METHOD__ . '/saveCart');

            Mage::dispatchEvent('checkout_cart_add_product', array('product'=>$product));

            $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
            /*if (!Mage::getSingleton('checkout/session')->getNoCartRedirect(true)) {
                Mage::getSingleton('checkout/session')->addSuccess($message);
                $this->_goBack();
            }*/
        }
        catch (Mage_Core_Exception $e) {
            if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
            }
            else {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
            }

            $url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            }
            else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
            $this->_goBack();
        }
    }

    /**
     * Update shoping cart data action
     */
    public function updatePostAction()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $cart = $this->_getCart();
                $cart->updateItems($cartData)
                    ->save();
            }
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        }
        catch (Mage_Core_Exception $e){
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
        }
        catch (Exception $e){
            Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot update shopping cart'));
        }
        $this->_goBack();
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                  ->save();
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addError($this->__('Cannot remove item'));
            }
        }
        $this->_redirectReferer(Mage::getUrl('*/*'));
    }

    /**
     * Initialize shipping information
     */
    public function estimatePostAction()
    {
        $country    = (string) $this->getRequest()->getParam('country_id');
        $postcode   = (string) $this->getRequest()->getParam('estimate_postcode');
        $city       = (string) $this->getRequest()->getParam('estimate_city');
        $regionId   = (string) $this->getRequest()->getParam('region_id');
        $region     = (string) $this->getRequest()->getParam('region');

        $this->_getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
        $this->_getQuote()->save();
        $this->_goBack();
    }

    public function estimateUpdatePostAction()
    {
        $code = (string) $this->getRequest()->getParam('estimate_method');
        if (!empty($code)) {
            $this->_getQuote()->getShippingAddress()->setShippingMethod($code)/*->collectTotals()*/->save();
        }
        $this->_goBack();
    }

    /**
     * Initialize coupon
     */
    public function couponPostAction()
    {
        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
        }

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : null)
                ->collectTotals()
                ->save();
            if ($couponCode) {
                if ($couponCode == $this->_getQuote()->getShippingAddress()->getCouponCode()) {
                    Mage::getSingleton('checkout/session')->addSuccess(
                        $this->__('Coupon code was applied successfully.')
                    );
                }
                else {
                    Mage::getSingleton('checkout/session')->addError(
                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
            } else {
                Mage::getSingleton('checkout/session')->addSuccess(
                    $this->__('Coupon code was canceled successfully.')
                );
            }

        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError(
                $this->__('Can not apply coupon code.')
            );
        }

        $this->_goBack();
    }
}