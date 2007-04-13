<?php
class Mage_Core_SearchController extends Mage_Core_Controller_Admin_Action
{
    function doAction()
    {
        for ($i=0; $i<10; $i++) {
            $items[] = array('id'=>'product/1/'.$i, 'type'=>'Product', 'name'=>'blah blah', 'description'=>'afg lasfh glsjfh glsfjg slfjhg sljfg lwjrht qprht');
        }
        $totalCount = 100;

        $data = array('totalCount'=>$totalCount, 'items'=>$items);
        $json = Zend_Json::encode($data);
        
        $this->getResponse()->setBody($json);
    }
}