<?php
class Mage_Core_WebsiteController extends Mage_Core_Controller_Admin_Action
{
    function listAction()
    {
        $data = array();
        //TODO: move node to JS
        $data[] = array(
            'value' => 0,
            'text'  => __('All websites')
        );
        $arrSites = Mage::getModel('core_resource', 'website_collection')->load();
        
        foreach ($arrSites as $website) {
            $data[] = array(
                'value' => $website->getWebsiteId(),
                'text'  => $website->getWebsiteCode()
            );
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    function treeListAction() {
        $data = array(
               array(
                    'id' => 1,
                    'text'  => 'website1'
                ),
                array(
                    'id' => 2,
                    'text'  => 'website2'
                ),
                array(
                    'id' => 3,
                    'text'  => 'website3'
                ),
                array(
                    'id' => 4,
                    'text'  => 'website4'
                )
        );
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
}