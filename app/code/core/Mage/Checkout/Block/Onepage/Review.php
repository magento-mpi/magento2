<?php
/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @subpackage Onepage
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Review extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('review', array('label'=>__('Order Review')));
        parent::_construct();
    }
}