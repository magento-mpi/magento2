<?php
class Mage_Core_WebsiteController extends Mage_Core_Controller_Admin_Action
{
    function listAction()
    {
        $data = array(
            'totalRecords' => 4,
            'websites' => array(
                array(
                    'value' => 1,
                    'text'  => 'website1'
                ),
                array(
                    'value' => 2,
                    'text'  => 'website2'
                ),
                array(
                    'value' => 3,
                    'text'  => 'website3'
                ),
                array(
                    'value' => 4,
                    'text'  => 'website4'
                )
            )
        );
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