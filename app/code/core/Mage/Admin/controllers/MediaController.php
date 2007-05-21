<?php

class Mage_Admin_MediaController extends Mage_Core_Controller_Front_Action
{
    public function foldersTreeAction()
    {
        $path = $this->getRequest()->getParam('node', '/');
        if (strpos($path, './')!==false) {
            die ("INVALID PATH");
        }
        if (DS!=='/') {
            $path = str_replace('/', DS, $path);
        }
        
        $root = Mage::getConfig()->getWebsiteDir('media');
        
        $arrNodes = array();
        $dir = dir($root.$path);
        while (false !== ($entry = $dir->read())) {
            if ('.'===$entry{0} || !is_dir($root.$path.$entry)) {
                continue;
            }
            
            $arrNodes[] = array(
                'text'=>$entry,
                'id'=>$path.$entry.'/',
                'cls'=>'folder',
                'iconCls'=>'folder',
                'leaf'=>false,
            );
        }
        $dir->close();
        
        $this->getResponse()->setBody(Zend_Json::encode($arrNodes));
    }
    
    public function filesGridAction()
    {
        
    }
    
}
