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
        $product->setStoreId($this->getRequest()->getParam('store', 0));
        
        if ($productId) {
            $product->load($productId);            
            $this->_addLeft(
                $this->getLayout()->createBlock('adminhtml/store_switcher')
                    ->setStores($product->getStoreIds())
            );
        }
        
        Mage::register('product', $product);
        
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_edit'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/catalog_product_edit_tabs'));
        
        $this->renderLayout();
    }
    
    public function relatedAction()
    {
        $this->_initProduct('related');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_related')->toHtml()
        );       
    }
    
    public function upsellAction()
    {
        $this->_initProduct('up_sell');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_upsell')->toHtml()
        );       
    }
    
    public function crosssellAction()
    {
        $this->_initProduct('cross_sell');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_crosssell')->toHtml()
        );       
    }
    
    protected function _initProduct($type)
    {
    	$productId  = (int) $this->getRequest()->getParam('id');
        $product    = Mage::getModel('catalog/product');
        
        if ($productId) {
            $product->load($productId);
        }        
               
        Mage::register('product', $product);
    }
    
    public function saveAction()
    {
        $storeId = $this->getRequest()->getParam('store');
        if ($data = $this->getRequest()->getPost()) {
            $categories = array();
            $stores     = array();
            $relatedProducts = array();
            $upSellProducts = array();
            $crossSellProducts = array();
            
            if(isset($data['categories'])) {
                $categories = explode(',', $data['categories']);
            }
            
            if (isset($data['stores'])) {
                $stores = $data['stores'];
            }

            if($this->getRequest()->getPost('_related_products')) {
            	$relatedProducts = $this->_decodeInput($this->getRequest()->getPost('_related_products'));
            } 
            
            if($this->getRequest()->getPost('_up_sell_products')) {
            	$upSellProducts = $this->_decodeInput($this->getRequest()->getPost('_up_sell_products'));
            } 
            
            if($this->getRequest()->getPost('_cross_sell_products')) {
            	$crossSellProducts = $this->_decodeInput($this->getRequest()->getPost('_cross_sell_products'));
            } 
            
        	$product = Mage::getModel('catalog/product')
                ->setData($data['product'])
                ->setId((int) $this->getRequest()->getParam('id'))
                ->setStoreId((int) $storeId)
                ->setAttributeSetId((int) $this->getRequest()->getParam('set'))
                ->setTypeId((int) $this->getRequest()->getParam('type'))
                ->setPostedStores($stores)
                ->setPostedCategories($categories)
                ->setRelatedProducts($relatedProducts)
                ->setUpSellProducts($upSellProducts)
                ->setCrossSellProducts($crossSellProducts);
                
            try {
                $product->save();
                Mage::getSingleton('adminhtml/session')->addSuccess('Product saved');
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage())
                    ->setProductData($data);
                $this->_redirect(Mage::getUrl('*/*/edit', array('id'=>$product->getId(), 'store'=>$storeId)));
                return;
            }
        }

        $this->getResponse()->setRedirect(Mage::getUrl('*/*/', array('store'=>$storeId)));
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
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')
                ->setId($id);
            try {
                $product->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess('Product deleted');
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));        
    }
    
    public function exportCsvAction()
    {
        
    }
    
    public function exportXmlAction()
    {
        
    }
}
