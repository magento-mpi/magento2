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
        $this->loadLayout();
        
        $categories = Mage::getResourceModel('catalog/category_tree')
            ->joinAttribute('name')
            ->load(1) // TODO: from config
            ->getNodes();
        $types = Mage::getModel('catalog/product_attribute')
            ->loadByCode('type')
            ->getSource()
                ->getArrOptions();
        $manufacturers = Mage::getModel('catalog/product_attribute')
            ->loadByCode('manufacturer')
            ->getSource()
                ->getArrOptions();
        
        $block = $this->getLayout()->createBlock('core/template', 'catalog.search.advanced')
            ->setTemplate('catalog/search/form.advanced.phtml')
            ->assign('action', Mage::getUrl('catalogsearch/advanced/result'))
            ->assign('categories', $categories)
            ->assign('types', $types)
            ->assign('manufacturers', $manufacturers);

        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    public function resultAction()
    {
        $this->loadLayout();
            
        $block = $this->getLayout()->createBlock('catalogsearch/search', 'search.result');
        $block->loadByAdvancedSearch($this->getRequest());
        
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
}
