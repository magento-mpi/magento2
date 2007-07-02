<?php
/**
 * Adminhtml customers page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customers extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/index.phtml');
    }
    
    public function _beforeToHtml()
    {
        $this->assign('createUrl', Mage::getUrl('adminhtml/customer/new'));
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/customer_grid', 'customer.grid'));
        return $this;
    }
}
