<?php
/**
 * Customer login block
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Block_Account extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('customer/account.phtml');
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(__('My Account'));
    }
}