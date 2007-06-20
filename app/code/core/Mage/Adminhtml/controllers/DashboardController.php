<?php
/**
 * Dashboard admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_DashboardController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('dashboard');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('dashboard'), __('dashboard title'));
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('core/template', 'dashboard.menu')->setTemplate('adminhtml/dashboard/left.phtml'));
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/dashboard', 'dashboard'));
        $this->renderLayout();
    }
    
    public function onlineAction() 
    {
       $collection = Mage::getSingleton('log_resource/visitor_collection')
                   ->useOnlineFilter()
                   ->load();
       
       foreach( $collection -> getItems() as $item ) {
            $item->setLocation(long2ip($item->getRemoteAddr()))
                 ->addCustomerData($item);
       
            if( $item->getCustomerId() > 0 ) {
                //print_r( $item );
                $item->setFullName( $item->getCustomerData()->getName() );
                
                // Not needed yet...
                // $adresses = $item->getCustomerData()->getAddressCollection()->getPrimaryAddresses();               
            } else {
                $item->setFullName('Guest');
            }
       }
        
       $this->getResponse()->setBody( $collection->toXml() );
    }

    
    public function visitorsAction()
    {
        $range = $this->getRequest()->getPost('range');
        
        $collection = Mage::getSingleton('log_resource/visitor_collection')
                    ->getStatistics('d')
                    ->applyDateRange($range['start'], $range['end'])
                    ->load();
        
        $this->getResponse()->setBody($collection->toXml());
    }
    
    public function quoteAction()
    {
        $collection = Mage::getModel('sales/quote')
                    ->load($this->getRequest()->getPost('quoteId',0));
        
        $this->getResponse()->setBody($collection->toXml());
    }
}
