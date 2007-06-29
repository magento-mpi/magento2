<?php
/**
 * config controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Customer_GroupController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction() 
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/group');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml',array('controller'=>'customer')))
            ->addLink(__('customers groups'), __('customers groups title'));
        
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/customer_group', 'group'));
        
        $this->renderLayout();
    }
    
    public function newAction() 
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/group');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml',array('controller'=>'customer')))
            ->addLink(__('customer groups'), __('customer groups title'), Mage::getUrl('adminhtml',array('controller'=>'customer_group')))
            ->addLink(__('new customer group'), __('new customer groups title'));
        
        $this->renderLayout();
    }
}
