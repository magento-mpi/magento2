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
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart abstract block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Checkout_Block_Cart_Abstract extends Mage_Core_Block_Template
{
    protected $_customer = null;
    protected $_checkout = null;
    protected $_quote    = null;

    protected $_totals;

    protected $_cartItemRenders = array();

    public function __construct()
    {
        parent::__construct();
        $this->addCartItemRender('default', 'checkout/cart_render_default', 'checkout/cart/render/default.phtml');
    }

    public function addCartItemRender($name, $block, $template)
    {
        $this->_cartItemRenders[$name] = array(
            'block' => $block,
            'template' => $template
        );
        return $this;
    }

    public function getCartItemRender($name)
    {
        if (isset($this->_cartItemRenders[$name])) {
            return $this->_cartItemRenders[$name];
        }
        return $this->_cartItemRenders['default'];
    }

    /**
     * Get logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_session
     */
    public function getCheckout()
    {
        if (null === $this->_checkout) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    /**
     * Get active quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }

    public function getTotals()
    {
        return $this->getTotalsCache();
    }

    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $this->_totals = $this->getQuote()->getTotals();
        }
        return $this->_totals;
    }

    /**
     * Get all cart items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getQuote()->getAllItems();
    }

    public function getCartItemHtml(Mage_Sales_Model_Quote_Item $item)
    {
        $itemRenderInfo = $this->getCartItemRender($item->getProduct()->getTypeId());
        $itemBlock = $this->getLayout()
            ->createBlock($itemRenderInfo['block'])
                ->setTemplate($itemRenderInfo['template'])
                ->setItem($item);

        return $itemBlock->toHtml();
    }

}