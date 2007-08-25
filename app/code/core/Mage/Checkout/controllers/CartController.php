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
        if ($this->getQuote()->hasItems()) {
        	$this->getQuote()->collectTotals()->save();
        } else {
        	$this->getQuote()->getShippingAddress()
        		->setCollectShippingRates(false)
        		->removeAllShippingRates();
        	$this->getQuote()->collectTotals()->save();
        }

        $this->loadLayout(array('default', 'cart'), 'cart');

        $this->renderLayout();
    }

    public function addAction()
    {
        $intFilter = new Zend_Filter_Int();
        $productId = $intFilter->filter($this->getRequest()->getParam('product'));
        $relatedProducts = $this->getRequest()->getParam('related_product');

        if (empty($productId)) {
            $this->_backToCart();
            return;
        }

        $qty = $intFilter->filter($this->getRequest()->getParam('qty', 1));

        $product = Mage::getModel('catalog/product')->load($productId);

        if ($product->getId()) {
            Mage::getSingleton('checkout/session')->setLastAddedProductId($product->getId());
        	if($product->isSuperConfig()) {
        		$productId = $product->getSuperLinkIdByOptions($this->getRequest()->getParam('super_attribute'));
        		if($productId) {
        			$superProduct = Mage::getModel('catalog/product')
        				->load($productId)
        				->setParentProduct($product);
        			if($superProduct->getId()) {
        				$item = $this->getQuote()->addCatalogProduct($superProduct->setQty($qty));
        				$item->setDescription(
		            		$this->getLayout()->createBlock('checkout/cart_item_super')->setSuperProduct($superProduct)->toHtml()
		            	);
		            	$item->setName($product->getName());
		            	$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
		            	$this->getQuote()->save();

        			}
        		} else {
        		    Mage::getSingleton('catalog/session')->addError('Specify product option, please');
        			$this->_backToProduct($product->getId());
        			return;
        		}

        	} else if($product->isSuperGroup()) {
        		$superGroupProducts = $this->getRequest()->getParam('super_group', array());
        		if(!is_array($superGroupProducts)) {
        			$superGroupProducts = array();
        		}

        		if(sizeof($superGroupProducts)==0) {
        		    Mage::getSingleton('catalog/session')->addError('Specify products, please');
        			$this->_backToProduct($product->getId());
        			return;
        		}

        		foreach($product->getSuperGroupProductsLoaded() as $superProductLink) {

        			if(isset($superGroupProducts[$superProductLink->getLinkedProductId()]) && $qty =  $intFilter->filter($superGroupProducts[$superProductLink->getLinkedProductId()])) {
      				   $superProduct = Mage::getModel('catalog/product')
	        				->load($superProductLink->getLinkedProductId())
	        				->setParentProduct($product);
	        			if($superProduct->getId()) {
	        				$this->getQuote()->addCatalogProduct($superProduct->setQty($qty));
	        			}
        			}
        		}
            	$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            	$this->getQuote()->save();


        	} else {
        	   	$this->getQuote()->addCatalogProduct($product->setQty($qty));
            	$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            	$this->getQuote()->save();
        	}
        }
        
        if ($relatedProducts) {
            $relatedProducts = explode(',', $relatedProducts);
            if (is_array($relatedProducts)) {
                foreach ($relatedProducts as $productId) {
                	$product = Mage::getModel('catalog/product')->load($productId);
            	   	$this->getQuote()->addCatalogProduct($product->setQty($qty));
                	$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                }
                $this->getQuote()->save();
            }
        }
        
        Mage::getSingleton('checkout/session')->setQuoteId($this->getQuote()->getId());

        $this->_backToCart();
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