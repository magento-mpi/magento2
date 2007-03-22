<?php
/**
 * Catalog admin index controller
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_IndexController extends Mage_Core_Controller_Admin_Action 
{
    /**
     * Index action (json)
     */
    public function indexAction() 
    {
        $this->loadLayout('admin', 'catalog_panel');
        
        $this->renderLayout();
        
        #Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/startPageLayout.json', 'mage_catalog');
    }
    
    /**
     * Load main panel (json)
     *
     */
    public function loadMainPanelAction()
    {
        #Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/loadMainPanel.json', 'mage_catalog');
    }
    
    /**
     * Load attributes panel (json)
     *
     */
    public function loadAttributesPanelAction()
    {
        #Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/attributes/loadPanel.json', 'mage_catalog');
    }
}
