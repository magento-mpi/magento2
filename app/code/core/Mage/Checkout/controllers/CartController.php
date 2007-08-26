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

    public function indexAction()
    {
    	#Mage::getSingleton('customer/session')->setTest('cart');

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

        $this->loadLayout(array('default', 'cart'), 'cart');

        $this->renderLayout();
    }

    public function addAction()
    {
        $productId       = (int) $this->getRequest()->getParam('product');
        $qty             = (int) $this->getRequest()->getParam('qty', 1);
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
                    $productIds[] = $relatedId;
                }
            }
        }
        
        $cart = Mage::getSingleton('checkout/cart');
        try {
            $cart->addProduct($productId, $qty);
            $cart->addAdditionalProducts($additionalIds);
            $cart->save();
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
        $cart = $this->getRequest()->getParam('cart');
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        if (is_array($cart)) {
            foreach ($cart as $id=>$itemUpd) {
                if (empty($itemUpd['qty']) || !is_numeric($itemUpd['qty']) || intval($itemUpd['qty'])<=0) {
                    continue;
                }

                $itemUpd['qty'] = (int) $itemUpd['qty'];

                if (!empty($itemUpd['remove'])) {
                    $this->getQuote()->removeItem($id);
                } else {
                    $item = $this->getQuote()->getItemById($id);
                    if (!$item) {
                        continue;
                    }
                    if (!empty($itemUpd['wishlist']) && !empty($customer)) {
                        if (empty($wishlist)) {
                            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
                        }
                        $wishlist->addNewItem($item->getProductId())->save();
                        $this->getQuote()->removeItem($id);
                        continue;
                    }

                    $product = Mage::getModel('catalog/product')->load($item->getProductId());
                    if($item->getParentProductId()) {
                        $parentProduct = Mage::getModel('catalog/product')->load($item->getParentProductId());
                        $product->setParentProduct($parentProduct);
                    }
                    $item->setQty($itemUpd['qty']);
                    $item->setPrice($product->getFinalPrice($item->getQty()));

                }
            }
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->getQuote()->save();
        }

        $this->_backToCart();
    }

    public function moveToWishlistAction()
    {
    	$customer = Mage::getSingleton('customer/session')->getCustomer();

        $id = $this->getRequest()->getParam('id');
		$item = $this->getQuote()->getItemById($id);
		if (!$item) {
	        continue;
	    }
	    if (!empty($itemUpd['wishlist']) && !empty($customer)) {
	        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
	        $wishlist->addNewItem($item->getProductId())->save();
	        $this->getQuote()->removeItem($id);
	    }

	    $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->save();

        $this->_backToCart();
    }

    public function deleteAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	try {
    		$this->getQuote()->removeItem($id)->save();
    	} catch (Exception $e) {

    	}

    	$this->_redirectToReferer();
    }

    public function cleanAction()
    {

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