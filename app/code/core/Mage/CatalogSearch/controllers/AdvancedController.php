<?php
/**
 * Catalog Search Controller
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_CatalogSearch_AdvancedController extends Mage_Core_Controller_Front_Action
{
  
    public function indexAction()
    {
        $this->loadLayout(array('default', 'catalogsearch_advanced_form'), 'catalogsearch_advanced_form');
        $this->renderLayout();
    }
    
    public function resultAction()
    {
        $this->loadLayout(array('default', 'catalogsearch_advanced_result'), 'catalogsearch_advanced_result');
        $this->renderLayout();
    }
}
