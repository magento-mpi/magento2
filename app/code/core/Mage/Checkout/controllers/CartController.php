<?php

class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action
{
    protected function _backToCart()
    {
        $this->_redirect('checkout/cart');
        return $this;
    }

    protected function _backToProduct($productId)
    {
        $this->_redirect('catalog/product/view', array('id'=>$productId));
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
        Mage::getSingleton('checkout/session')->resetCheckout();
        if (!$this->getQuote()->hasItems()) {
        	$this->getQuote()->getShippingAddress()
        		->setCollectShippingRates(false)
        		->removeAllShippingRates();

        	$this->getQuote()
        		->removeAllAddresses()
        		->removePayment();
        }
        $this->getQuote()->collectTotals()->save();

        if (!$this->_getCart()->isValidItemsQty()) {
            Mage::getSingleton('checkout/session')
                ->addError('The item (s) marked in red are not available in the desired quantity. Please update the quantity of the item (s).');
        }
        $this->loadLayout(array('default', 'cart'), 'cart');
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
    }

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

        $cart = $this->_getCart();
        try {
            $product = Mage::getModel('catalog/product')
                ->load($productId)
                ->setConfiguredAttributes($this->getRequest()->getParam('super_attribute'))
                ->setGroupedProducts($this->getRequest()->getParam('super_group', array()));

            $cart->addProduct($product, $qty)
                ->addProductsByIds($additionalIds)
                ->save();
            $this->_backToCart();
        }
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            $url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            }
            else {
                $this->_backToCart();
            }
        }
    }

    public function updatePostAction()
    {
        $cartData = $this->getRequest()->getParam('cart');
        $cart = $this->_getCart();
        try {
            $cart->updateItems($cartData)
                ->save();
        }
        catch (Exception $e){
            Mage::getSingleton('checkout/session')->addError('Cannot update shopping cart');
        }
        $this->_backToCart();

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $this->_backToCart();
    }

    public function moveToWishlistAction()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $this->_getCart()->moveItemToWishlist($id)
                ->save();
        }
        catch (Exception $e){
            Mage::getSingleton('checkout/session')->addError('Cannot move item to wishlist');
        }
        $this->_backToCart();
    }

    public function deleteAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	$cart = Mage::getSingleton('checkout/cart');
    	try {
    		$cart->removeItem($id)
    		  ->save();
    	} catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError('Cannot remove item');
    	}

    	$this->_redirectToReferer();
    }

    public function estimatePostAction()
    {
        $postcode = $this->getRequest()->getParam('estimate_postcode');

        $this->getQuote()->getShippingAddress()
            ->setPostcode($postcode)->setCollectShippingRates(true);

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