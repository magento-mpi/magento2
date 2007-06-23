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
       $collection = Mage::getResourceSingleton('log/visitor_collection')
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
        
        $collection = Mage::getResourceSingleton('log/visitor_collection')
                    ->getStatistics('d')
                    ->applyDateRange($range['start'], $range['end'])
                    ->load();
        
        $this->getResponse()->setBody($collection->toXml());
    }
    
    public function visitorsLiveAction()
    {
        $minimum = time() - 12 * 60 * 60;
        $maximum = time() +  15 * 60;
        
        $allData = new Varien_Object();
        $allData->setMinimum(date('Y/m/d H:i', $minimum ));
        $allData->setMaximum(date('Y/m/d H:i', $maximum ));
        
        $logItem = new Varien_Object();
        
        $logXML = '';
        
        // Last 11 hours by hours
        for($i = $minimum; $i < $maximum - ( 15*60 + 60*60 ); $i += 60*60) {
            $visitors  = rand( 1, 100000 );
            $customers = rand( 0, $visitors);
            $logItem->addData(array(
                'time'      => date('Y/m/d H:i', $i),
                'customers' => $customers,
                'visitors'  => $visitors
            ));
            
            $logXML.= $logItem->toXml(array(), 'item', false, true);
        }
        
        // Last hour by 5 minutes
        for($i = time() - 60*60; $i < time(); $i += 5*60) {
            $visitors  = rand( 1, 100000 );
            $customers = rand( 0, $visitors);
            $logItem->addData(array(
                'time'      => date('Y/m/d H:i', $i),
                'customers' => $customers,
                'visitors'  => $visitors
            ));
            
            $logXML.= $logItem->toXml(array(), 'item', false, true);
        }
        
        $allData->setItems($logXML);
        
        $this->getResponse()->setBody($allData->toXml(array(),'dataSource',true,false));
    }
    
    public function visitorsReportAction()
    {
        // Not implemented yet
        $this->getResponse()->setBody( 'Not implemented yet' );
    }
    
    public function visitorsLiveUpdateAction()
    {
        $minimum = time() - 12 * 60 * 60;
        $maximum = time() +  15 * 60;
        
        $visitors = rand( 1, 100000 );
        $customers = rand( 0, $visitors);
        
        $updateData = new Varien_Object();
        $updateData->setMinimum(date('Y/m/d H:i', $minimum ));
        $updateData->setMaximum(date('Y/m/d H:i', $maximum ));
        $updateData->setTime(date('Y/m/d H:i', time() ));
        $updateData->setCustomers($customers);
        $updateData->setVisitors($visitors);
        
        $this->getResponse()->setBody($updateData->toXml(array(),'update',true,false));
    }
    
    public function quoteAction()
    {
        $quote = Mage::getModel('sales/quote')
               ->load($this->getRequest()->getParam('quoteId',0));
        
       
        
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        $itemsFilter->addFilter(Mage::getSingleton('core/store')->getPriceFilter(), 'price');
        $itemsFilter->addFilter(Mage::getSingleton('core/store')->getPriceFilter(), 'row_total');
        $cartItems = $itemsFilter->filter($quote->getItems());

        $totalsFilter = new Varien_Filter_Array_Grid();
        $totalsFilter->addFilter(Mage::getSingleton('core/store')->getPriceFilter(), 'value');
        $cartTotals = $totalsFilter->filter($quote->getTotals());
        
        // Creating XML response. 
        // In future would be good if it will be in some collection.
        $itemsXML = "";
        
        $itemObject = new Varien_Object();
        $xmlObject = new Varien_Object();
        
        foreach( $cartItems as $cartItem ) {
            $itemObject->addData($cartItem);
            $itemObject->setUrl(Mage::getUrl('catalog', array('controller'=>'product', 'action'=>'view', 
                                                               'id'=>$itemObject->getProductId())));
            $itemsXML.= $itemObject->toXml(array('price', 'qty', 'row_total', 'name', 'url'), "item", false, true);
        }
        
        $xmlObject->setItems( $itemsXML );
        
        $totalXML = "";
        $totalObject = new Varien_Object();
        
        foreach( $cartTotals as $cartTotal ) {
            $totalObject->addData( $cartTotal );
            $totalXML.= $totalObject->toXml(array('title','value'), 'total', false, true);
        }
        
        $xmlObject->setTotals( $totalXML );
        $this->getResponse()->setBody( $xmlObject->toXml(array(), 'dataSource', true, false) );
    }
}
