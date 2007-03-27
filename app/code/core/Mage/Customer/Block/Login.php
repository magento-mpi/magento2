<?php
/**
 * Customer login block
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Block_Login extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setViewName('Mage_Customer', 'login');
        
    }
}