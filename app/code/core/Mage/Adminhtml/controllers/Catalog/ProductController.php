<?php
/**
 * Catalog product controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/products');
        
        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/catalog_product')
        );
        
        $this->renderLayout();
    }
    
    public function gridAction()
    {
        
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/products');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);
        
        $productId  = (int) $this->getRequest()->getParam('id');
        $product    = Mage::getModel('catalog/product');
        
        if ($productId) {
            $product->load($productId);
        }
        
        Mage::register('product', $product);
        
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_edit'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/catalog_product_edit_tabs'));
        
        $this->renderLayout();
    }
    
    public function relatedAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_related')->toHtml()
        );       
    }
    
    protected function _initProduct()
    {
    	$productId  = (int) $this->getRequest()->getParam('id');
        $product    = Mage::getModel('catalog/product');
        
        if ($productId) {
            $product->load($productId);
            $product->getRelatedProducts()->load();
        }

        
               
        Mage::register('product', $product);
    }
    
    public function saveAction()
    {
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        
        if($this->getRequest()->getParam('_related_products')) {
	        $relatedProducts = $this->_decodeInput($this->getRequest()->getParam('_related_products'));
	    }
	    
        
    }
    
    /**
     * Decode strings for linked products
     *
     * @param 	string $encoded
     * @return 	array
     */
    protected function _decodeInput($encoded)
    {
    	parse_str($encoded, $data);
        foreach($data as $key=>$value) {
        	parse_str(base64_decode($value), $data[$key]);
        }
        
        return $data;
    }
        
    
    public function deleteAction()
    {
        
    }
    
    public function exportCsvAction()
    {
        
    }
    
    public function exportXmlAction()
    {
        
    }
}
