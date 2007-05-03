<?php

class Mage_Sales_OrderController extends Mage_Core_Controller_Admin_Action
{
    public function gridAction()
    {
        $quotes = Mage::getModel('sales_resource', 'quote_collection');
        $quotes->addAttributeSelect('self');
        $quotes->addAttributeSelect('item', 'row_total');
        $quotes->loadData();
        echo "<pre>"; print_r($quotes->getItems()); die;
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
}