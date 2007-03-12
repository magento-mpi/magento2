<?php




class Mage_Catalog_IndexController extends Mage_Core_Controller_Admin_Action 
{
    function indexAction() 
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/startPageLayout.json', 'mage_catalog');
    }

    function loadMainPanelAction()
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/loadMainPanel.json', 'mage_catalog');
        #$this->renderLayout('layout', 'toJs');
    }
    
    function catalogTreeAction()
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/catalogTree.json', 'mage_catalog');
    }

    function __call($method, $args) 
    {
        var_dump($method);
    }
}
