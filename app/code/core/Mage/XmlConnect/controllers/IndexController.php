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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect index controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Declare content type header
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    }

    /**
     * Default action
     *
     */
    public function indexAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Category list
     *
     */
    public function categoryAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Filter product list
     *
     */
    public function filtersAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Product information
     *
     */
    public function productAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Product options list
     *
     */
    public function optionsAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }


    /**
     * Product gallery images list
     *
     */
    public function galleryAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Product reviews list
     *
     */
    public function reviewsAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Add new review
     *
     */
    public function reviewAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Shopping cart display action
     */
    public function shoppingCartAction()
    {
        $messages = array();
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if (!$this->_getQuote()->validateMinimumAmount()) {
                $warning = Mage::getStoreConfig('sales/minimum_order/description');
                $messages[Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_WARNING][] = $warning;
            }
        }

        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                $messages[Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_SUCCESS][] = $message;
            }
        }

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);

        $this->loadLayout(false)->getLayout()->getBlock('xmlconnect.cart')->setMessages($messages);
        $this->renderLayout();
    }

    /**
     * Delete item from shopping cart action
     *
     */
    public function deleteFromCart()
    {

    }

    /**
     * Add product to shopping cart action
     */
    public function addToCartAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = null;
            $productId = (int) $this->getRequest()->getParam('product');
            if ($productId) {
                $_product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                if ($_product->getId()) {
                    $product = $_product;
                }
            }
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_message($this->__('This product is unavailable.'), Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_ERROR);
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_message($message, Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_SUCCESS);
                }
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_ERROR);
        }
        catch (Exception $e) {
            $this->_message($this->__('Cannot add the item to shopping cart.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteFromCartAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)->save();
                $this->_message('Item was successfully deleted from shopping cart.', Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_SUCCESS);
            }
            catch (Mage_Core_Exception $e) {
                $this->_message($e->getMessage(), Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_ERROR);
            }
            catch (Exception $e) {
                $this->_message($this->__('Cannot remove the item.'), self::MESSAGE_STATUS_ERROR);
            }
        }
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
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    /**
     * Generate message xml and set it to response body
     * @param string $text
     * @param string $status
     */
    protected function _message($text, $status, $type='', $action='')
    {
        $message = new Varien_Simplexml_Element('<message></message>');
        $message->addChild('status', $status);
        $message->addChild('text', $text);
        $this->getResponse()->setBody($message->asNiceXml());
    }
}