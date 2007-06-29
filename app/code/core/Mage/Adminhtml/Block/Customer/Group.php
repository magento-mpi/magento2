<?
/**
 * Adminhtml customers group page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Group extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/group/list.phtml');
    }
    
    public function _beforeToHtml()
    {
        $this->assign('grid', $this->getLayout()->createBlock('adminhtml/customer_group_grid', 'customer.group.grid')->toHtml());
        return $this;
    }
   
}
