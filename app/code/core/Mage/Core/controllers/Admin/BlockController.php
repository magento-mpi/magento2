<?php

require_once('Zend/Filter.php');
require_once('Zend/Filter/Alpha.php');
require_once('Zend/Filter/StringToLower.php');

class BlockController extends Mage_Core_Controller_Admin_Action
{
    function indexAction()
    {
        echo "test";
    }
    
    function loadPanelAction()
    {
       # $this->renderLayout('layout', 'toJs');
    }
    
    function loadTreeAction() {
        $this->_view->setScriptPath(Mage::getRoot('layout').'/Admin');
        $this->_view->assign('BASE_URL', Mage::getBaseUrl());
        $this->getResponse()->appendBody($this->_view->render('/core/block.tree.phtml'));
    }
    
    function nodeAction() {
        $node = ($_POST['node'] == ".")?'':$_POST['node'];
        
        if(substr($node, -5) == '.json') {
            $this->_forward('layoutNode');
            return true;
        }
        
        $root = dir(Mage::getRoot('layout'). DIRECTORY_SEPARATOR . $node);
        $content = array();
        while (false !== ($entry = $root->read())) {
            if ($entry{0} == ".") {
                continue;
            }
            $tmp = array();

            $tmp['text'] = $entry;
            if (is_file($root->path . DIRECTORY_SEPARATOR . $entry)) {
                if(substr($root->path . DIRECTORY_SEPARATOR . $entry, -5) != '.json') {
                    $tmp['leaf'] = 'true';
                }
                $tmp['id'] = $node . $entry;
            } else {
                $tmp['id'] = $node . $entry . DIRECTORY_SEPARATOR;
            }
            $content[] = $tmp;
        }
        $root->close();
        $json = Zend_Json::encode($content);
        $this->getResponse()->setBody($json);
    } 
    
    function layoutNodeAction() {
        
        $node = $_POST['node'];

        Mage_Core_Block::loadJsonFile($node);        
        $res = Mage_Core_Block::getAllBlocks();
        foreach($res as $block) {
            echo get_class($block)."<br>";
        }
        
        //var_dump($arr);       
        $tmp['text'] = 'blocks';
        $tmp['id'] = 'rootBlock';
        $tmp['leaf'] = 'true';
        $content[] = $tmp;
        $json = Zend_Json::encode($content);
        echo $json;
        //$this->getResponse()->setBody($json);
        exit();
    }
}
