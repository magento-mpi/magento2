<?php
/**
 * Adminhtml online customers page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Online extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('customer/online.phtml');
    }
    
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/customer_online_grid', 'customer.grid'));
        return $this;
    }
   
}
