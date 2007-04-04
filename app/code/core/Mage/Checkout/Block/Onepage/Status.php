<?php
/**
 * One page checkout status
 *
 * @package    Ecom
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Status extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setViewName('Mage_Checkout', 'onepage/status.phtml');
        
        $this->assign('billing', Mage::registry('Mage_Checkout')->getStateData('billing', 'data'));
        $this->assign('payment', Mage::registry('Mage_Checkout')->getStateData('payment', 'data'));
        $this->assign('shipping', Mage::registry('Mage_Checkout')->getStateData('shipping', 'data'));
        $this->assign('shipping_method', Mage::registry('Mage_Checkout')->getStateData('shipping_method', 'data'));
    }
}