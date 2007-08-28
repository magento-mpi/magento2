<?php
/**
 * Links block
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Checkout_Block_Links extends Mage_Core_Block_Template
{
    public function addCartLink()
    {
        $count = Mage::getSingleton('checkout/session')->getQuote()->getItemsSummaryQty();
        
        if( $count > 1 ) {
            $text = __('My Cart (%d items)', $count);
        } elseif( $count == 1 ) {
            $text = __('My Cart (%d item)', $count);
        } else {
            $text = __('My Cart');
        }

        $this->getParentBlock()->addLink(null, 'href="'.Mage::getUrl('checkout/cart').'"', $text);
    }

    public function addCheckoutLink()
    {
        $this->getParentBlock()->addLink(null, 'href="'.Mage::getUrl('checkout').'"', __('Checkout'));
    }
}