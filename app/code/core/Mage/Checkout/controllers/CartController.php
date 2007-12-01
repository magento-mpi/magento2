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


class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action
{
    protected function _backToCart()
    {
        $this->_redirect('checkout/cart');
        return $this;
    }

    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    public function indexAction()
    {
        Varien_Profiler::start('GETCART: '.__METHOD__);
        $cart = $this->_getCart();
        Varien_Profiler::stop('GETCART: '.__METHOD__);
        Varien_Profiler::start('INITCART: '.__METHOD__);
        $cart->init();
        Varien_Profiler::stop('INITCART: '.__METHOD__);
        Varien_Profiler::start('SAVECART: '.__METHOD__);
        $cart->save();
        Varien_Profiler::stop('SAVECART: '.__METHOD__);

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
    }
    
    /**
     * Adding product to shopping cart action
     */
    public function addAction()
    {
        $productId       = (int) $this->getRequest()->getParam('product');
        $qty             = $this->getRequest()->getParam('qty', 1);
        $relatedProducts = $this->getRequest()->getParam('related_product');

        if (!$productId) {
            $this->_backToCart();
            return;
        }

        $additionalIds = array();
        // Parse related products
        if ($relatedProducts) {
            $relatedProducts = explode(',', $relatedProducts);
            if (is_array($relatedProducts)) {
                foreach ($relatedProducts as $relatedId) {
                    $additionalIds[] = $relatedId;
                }
            }
        }

        try {
            $cart = $this->_getCart();
            $product = Mage::getModel('catalog/product')
                ->load($productId)
                ->setConfiguredAttributes($this->getRequest()->getParam('super_attribute'))
                ->setGroupedProducts($this->getRequest()->getParam('super_group', array()));

            $cart->addProduct($product, $qty)
                ->addProductsByIds($additionalIds)
                ->save();
            $this->_backToCart();
        }
        catch (Mage_Core_Exception $e){
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
            Mage::getSingleton('checkout/session')->addException($e, __('Can not add item to shopping cart'));
            $this->_backToCart();
        }
    }
    
    /**
     * Update shoping cart data action
     */
    public function updatePostAction()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            $cart = $this->_getCart();
            $cart->updateItems($cartData)
                ->save();
        }
        catch (Mage_Core_Exception $e){
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
        }
        catch (Exception $e){
            Mage::getSingleton('checkout/session')->addException($e, __('Cannot update shopping cart'));
        }
        
        $this->_backToCart();
    }
    
    /**
     * Move shopping cart item to wishlist action
     */
    public function moveToWishlistAction()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $this->_getCart()->moveItemToWishlist($id)
                ->save();
        }
        catch (Exception $e){
            Mage::getSingleton('checkout/session')->addError(__('Cannot move item to wishlist'));
        }
        $this->_backToCart();
    }
    
    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	$cart = Mage::getSingleton('checkout/cart');
    	try {
    		$cart->removeItem($id)
    		  ->save();
    	} catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError(__('Cannot remove item'));
    	}

    	$this->_redirectReferer(Mage::getUrl('*/*'));
    }

    public function estimatePostAction()
    {
        $country = $this->getRequest()->getParam('country_id');
        $postcode = $this->getRequest()->getParam('estimate_postcode');

        $this->getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setPostcode($postcode)
            ->setCollectShippingRates(true);

        $this->getQuote()/*->collectTotals()*/->save();

        $this->_backToCart();
    }

    public function estimateUpdatePostAction()
    {
        $code = $this->getRequest()->getParam('estimate_method');

        $this->getQuote()->getShippingAddress()->setShippingMethod($code)/*->collectTotals()*/->save();

        $this->_backToCart();
    }

    public function couponPostAction()
    {
        if ($this->getRequest()->getParam('do')==__('Clear')) {
            $couponCode = '';
        } else {
            $couponCode = $this->getRequest()->getParam('coupon_code');
        }

        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->setCouponCode($couponCode)->save();

        $this->_backToCart();
    }

    public function giftCertPostAction()
    {
        if ($this->getRequest()->getParam('do')==__('Clear')) {
            $giftCode = '';
        } else {
            $giftCode = $this->getRequest()->getParam('giftcert_code');
        }

        $this->getQuote()->setGiftcertCode($giftCode)/*->collectTotals()*/->save();

        $this->_backToCart();
    }
}