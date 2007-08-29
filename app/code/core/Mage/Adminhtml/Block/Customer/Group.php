<?
/**
 * Adminhtml customers group page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Group extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customer/group/list.phtml');
    }

    public function _beforeToHtml()
    {
        $this->assign('createUrl', Mage::getUrl('*/customer_group/new'));
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/customer_group_grid', 'customer.group.grid'));
        return parent::_beforeToHtml();
    }
}
