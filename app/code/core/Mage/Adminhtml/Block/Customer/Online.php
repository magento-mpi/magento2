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
        $this->setTemplate('adminhtml/customer/online.phtml');
    }
    
    public function _beforeToHtml()
    {
        $this->assign('grid', $this->getLayout()->createBlock('adminhtml/customer_OnlineGrid', 'customer.grid')->toHtml());
        return $this;
    }
   
}
