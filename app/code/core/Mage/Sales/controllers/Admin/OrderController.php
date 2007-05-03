<?php

class Mage_Sales_OrderController extends Mage_Core_Controller_Admin_Action
{
    public function gridAction()
    {
        $websiteId = $this->getRequest()->getParam('siteid', '');
        $orderStatus = $this->getRequest()->getParam('orderstatus', '');
        $pageSize = $this->getRequest()->getPost('pageSize', '');
        $sort = $this->getRequest()->getPost('sort', '');
        $dir = $this->getRequest()->getPost('dir', '');

        $orders = Mage::getModel('sales_resource', 'order_collection')
            ->addAttributeSelect('self/real_order_id')
            ->addAttributeSelect('self/customer_id')
            ->addAttributeSelect('self/grand_total')
            ->addAttributeSelect('self/status')
            ->addAttributeSelect('self/created_at')
            ->addAttributeSelect('self/website_id')
            ->addAttributeSelect('address/address_type')
            ->addAttributeSelect('address/firstname')
            ->addAttributeSelect('address/lastname');
            
        $orders->addAttributeFilter('address/address_type', 'billing');
        

        if (!empty($websiteId) && is_numeric($websiteId)) {
            $orders->addAttributeFilter('self/website_id', $websiteId);
        }
        if (!empty($orderStatus)) {
            $orders->addAttributeFilter('self/status', $orderStatus);
        }

        $orders->setPageSize($pageSize);
        
        if (!empty($sort)) {
            if ($sort=='firstname' || $sort=='lastname') {
                $sort = 'address/'.$sort;
            } else {
                $sort = 'self/'.$sort;
            }
            $orders->setOrder($sort, $dir);
        }
        
        $orders->loadData();
        $data['totalRecords'] = $orders->getSize();
        $data['items'] = array();
        
        $currency = new Varien_Filter_Sprintf('$%s', 2);
        foreach ($orders as $order) {
            $r = $order->getData();
            $r['grand_total'] = $currency->filter($r['grand_total']);

            $billing = $order->getAddressByType('billing');
            $r['firstname'] = $billing->getFirstname();
            $r['lastname'] = $billing->getLastname();
            
            $data['items'][] = $r;
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    public function treeAction()
    {
        $parent = $this->getRequest()->getParam('node', '');
        $data = array();
        
        if ($parent==='wsroot') {
            $data = array(array(
                'id' => 'all',
                'text'  => __('All websites'),
            ));
            $arrSites = Mage::getModel('core_resource', 'website_collection')->load();
            foreach ($arrSites as $website) {
                $data[] = array(
                    'id' => $website->getWebsiteId(),
                    'siteId' => $website->getWebsiteId(),
                    'text'  => $website->getWebsiteCode()
                );
            } 
        } else {
            $statuses = Mage::getConfig()->getNode('sales/order/statuses');
            foreach ($statuses->children() as $status) {
                $data[] = array(
                    'id' => $parent.'/'.$status->getName(),
                    'siteId' => $parent,
                    'orderStatus' => $status->getName(),
                    'text'  => (string)$status->title,
                    'leaf' => true,
                );
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    public function formAction()
    {
        $id = $this->getRequest()->getPost('id', 0);
        $order = Mage::getModel('sales', 'order')->load($id);
        
        $block = $this->getLayout()->createBlock('tpl', 'order.view')
            ->setTemplate('sales/order/view.phtml')
            ->assign('order', $order)
            ->assign('formAction', Mage::getBaseUrl().'mage_sales/order/save/');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function saveOrderAction()
    {
        $this->getResponse()->setRedirect(Mage::getBaseUrl().'mage_sales/order/form/');
    }
}