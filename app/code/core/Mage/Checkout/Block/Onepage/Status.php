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
    }
    
    protected function _initStatus()
    {
        $statuses = array(
            'billing',
            'payment',
            'shipping',
            'shipping_method',
        );
    }
}