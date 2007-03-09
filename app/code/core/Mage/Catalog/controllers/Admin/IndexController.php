<?php




class Mage_Catalog_IndexController extends Mage_Core_Controller_Admin_Action 
{
    function indexAction() 
    {

    }

    function initBlockAction() 
    {
        $categories = Mage::getModel('catalog','categories');
        $data = $categories->getTree();
        $tree = new Varien_Widget_HTMLTree($data);
        $tree->setHtmlId('catalog_tree');
        //$this->getResponse()->appendBody('<script src="'.Mage::getBaseUrl('skin').'/catalog/js/tree.js"></script>');
        $this->getResponse()->appendBody($tree->render());
    }
    
    function loadMainPanelAction()
    {
       #$this->renderLayout('layout', 'toJs');
    }

    function __call($method, $args) 
    {
        var_dump($method);
    }
}
