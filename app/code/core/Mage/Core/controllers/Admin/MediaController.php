<?php

class Mage_Core_MediaController extends Mage_Core_Controller_Admin_Action
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
        
        $root = Mage::getConfig()->getBaseDir('var').'/media';
        
        $arrNodes = array();
        $dir = dir($root.$path);
        while (false !== ($entry = $dir->read())) {
            if ('.'===$entry || '..'===$entry || !is_dir($root.$path.$entry)) {
                continue;
            }
            $node = array();
            $node['text']   = $entry;
            $node['id']     = $path.$entry.'/';
            $node['cls']    = 'folder';
            $node['leaf']   = false;
            
            $arrNodes[] = $node;
        }
        $dir->close();
        
        $this->getResponse()->setBody(Zend_Json::encode($arrNodes));
    }
    
    public function filesGridAction()
    {
        
    }
    
}
