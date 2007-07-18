<?php
class Mage_Admin_StoreController extends Mage_Core_Controller_Admin_Action
{
    function listAction()
    {
        $data = array();
        //TODO: move node to JS
        $data[] = array(
            'value' => 0,
            'text'  => __('All Stores')
        );
        $arrSites = Mage::getResourceModel('core/store_collection')->load();
        
        foreach ($arrSites as $store) {
            $data[] = array(
                'value' => $store->getStoreId(),
                'text'  => $store->getStoreCode()
            );
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    function treeListAction() {
        $data = array(
               array(
                    'id' => 1,
                    'text'  => 'store1'
                ),
                array(
                    'id' => 2,
                    'text'  => 'store2'
                ),
                array(
                    'id' => 3,
                    'text'  => 'store3'
                ),
                array(
                    'id' => 4,
                    'text'  => 'store4'
                )
        );
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
}