<?php
/**
 * Customer admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_CustomerController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Customers list action
     */
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        
        $this->getLayout()->getBlock('menu')->setActive('customer');
        //$this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/customer_left'));
        
        $block = $this->getLayout()->createBlock('adminhtml/customers', 'customers');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'));

        $this->renderLayout();
    }
    
    /**
     * Customer view action
     */
    public function viewAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/new');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml', array('controller'=>'customer')))
            ->addLink(__('new customer'), __('new customer title'));
            
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('adminhtml/customer_edit')
        );
        
        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/store_switcher'))
            ->append($this->getLayout()->createBlock('adminhtml/customer_tabs'));
        
        $this->renderLayout();
    }
    
    /**
     * Create new customer action
     */
    public function newAction()
    {
        $this->_forward('view');
    }

    public function onlineAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/online');
        $block = $this->getLayout()->createBlock('adminhtml/customer_online', 'customer_online');
        $this->getLayout()->getBlock('content')->append($block);

        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'));

        $this->renderLayout();


        $collection = Mage::getResourceSingleton('log/visitor_collection')
            ->useOnlineFilter()
            ->load();

        foreach ($collection->getItems() as $item) {
        	$item->addIpData($item)
                 ->addCustomerData($item)
        	     ->addQuoteData($item);
        }
    }

}
