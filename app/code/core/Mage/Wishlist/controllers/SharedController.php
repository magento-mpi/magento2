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
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist shared items controllers
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Wishlist_SharedController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {

        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCode($this->getRequest()->getParam('code'));
        if ($wishlist->getCustomerId() && $wishlist->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId()) {
            $this->_redirectUrl(Mage::helper('wishlist')->getListUrl());
            return;
        }

        if(!$wishlist->getId()) {
            $this->norouteAction();
        } else {
            Mage::register('shared_wishlist', $wishlist);
            $this->loadLayout();
            $this->_initLayoutMessages('wishlist/session');
            $this->getLayout()->getBlock('content')
                ->append(
                    $this->getLayout()->createBlock('wishlist/share_wishlist','customer.wishlist')
            );
            $this->renderLayout();
        }

    }

    public function allcartAction()
    {
        $wishlist = Mage::getModel('wishlist/wishlist')
            ->loadByCode($this->getRequest()->getParam('code'));
        Mage::getSingleton('checkout/session')->setSharedWishlist($this->getRequest()->getParam('code'));

        //exit($wishlist->getId());

        if (!$wishlist->getId()) {
            $this->norouteAction();
        } else {
            $wishlist->getProductCollection()->load();

            foreach ($wishlist->getProductCollection() as $item) {
                try {
                    $product = Mage::getModel('catalog/product')->load($item->getProductId())->setQty(1);
                    if ($product->isSalable()){
                        Mage::getSingleton('checkout/cart')->addProduct($product);
                        $item->delete();
                    }
                } catch(Exception $e) {
                    $url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
                    if ($url){
                        $url = Mage::getModel('core/url')->getUrl('catalog/product/view', array(
                            'id'=>$item->getProductId(),
                            'wishlist_next'=>1
                        ));

                        $urls[] = $url;
                        $messages[] = $e->getMessage();
                        $wishlistIds[] = $item->getId();
                    } else {
                        $item->delete();
                    }
                    /*
                    if($product) {
                        $lastError = array('product'=>$product, 'exception'=>$e);
                    } else {
                        $lastError = array('exception'=>$e);
                    }
                    */
                }


                Mage::getSingleton('checkout/cart')->save();
            }

            if ($urls) {
                Mage::getSingleton('checkout/session')->addError(array_shift($messages));
                $this->getResponse()->setRedirect(array_shift($urls));

                Mage::getSingleton('checkout/session')->setWishlistPendingUrls($urls);
                Mage::getSingleton('checkout/session')->setWishlistPendingMessages($messages);
                Mage::getSingleton('checkout/session')->setWishlistIds($wishlistIds);
            } else {
                $this->_redirect('checkout/cart');
            }

            /*
            if (isset($lastError) && !Mage::getSingleton('checkout/cart')->getQuote()->hasItems()) {
                Mage::getSingleton('catalog/session')->addError($lastError['exception']->getMessage());
                if(isset($lastError['product'])) {
                    // Redirect to the last product with exception
                    $this->getResponse()->setRedirect(Mage::helper('catalog/product')->getProductUrl($lastError['product']));
                } else {
                    $this->_redirect('catalog');
                }
            } else if (isset($lastError)) {
                Mage::getSingleton('checkout/session')->addError(Mage::getSingleton('checkout/cart')->getLastQuoteMessage());
            }

            $this->_redirect('checkout/cart');
            */
        }
    }

}
