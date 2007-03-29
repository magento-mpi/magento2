<?php



/**
 * Product View block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Product_View extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setViewName('Mage_Catalog', 'product/view.phtml');
    }

    public function loadData(Zend_Controller_Request_Http $request)
    {
        $productId = $request->getParam('id');
        $product = Mage::getModel('catalog', 'product', array('id'=>$productId));
        $product->load($productId);
        
        $breadcrumbs = Mage::createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>'Home','title'=>'Go to home page','link'=>Mage::getBaseUrl().'/'));
        $breadcrumbs->addCrumb('category', array('label'=>$product->getCategoryName(), 'title'=>'', 'link'=>$product->getCategoryLink()));
        $breadcrumbs->addCrumb('product', array('label'=>$product->getName()));
        $this->setChild('breadcrumbs', $breadcrumbs);
        
        $this->assign('product', $product);
    }
}