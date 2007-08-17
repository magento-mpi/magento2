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
        $product    = Mage::getModel('catalog/product')
            ->setStoreId($this->getRequest()->getParam('store', 0));
        if ($setId = (int) $this->getRequest()->getParam('set')) {
            $product->setAttributeSetId($setId);
        } 
        
        if ($typeId = (int) $this->getRequest()->getParam('type'))
        {
        	$product->setTypeId($typeId);	
        }
        
        if ($attributes = $this->getRequest()->getParam('attributes'))
        {
        	$product->setSuperAttributesIds(explode(",", base64_decode(urldecode($attributes))));
        }
                
        if ($productId) {
            $product->load($productId);
            $this->_addLeft(
                $this->getLayout()->createBlock('adminhtml/store_switcher')
                    ->setStoreIds($product->getStoreIds())
                    ->setSwitchUrl(Mage::getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'store'=>null)))
            );
        }
        
        Mage::register('product', $product);
        
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_edit'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/catalog_product_edit_tabs', 'product_tabs'));
        $this->_addJs($this->getLayout()->createBlock('core/template')->setTemplate('catalog/product/js.phtml'));
        
        $this->renderLayout();
    }
    
    public function relatedAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_related')->toHtml()
        );       
    }
    
    public function upsellAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_upsell')->toHtml()
        );       
    }
    
    public function crosssellAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_crosssell')->toHtml()
        );       
    }
    
    public function bundleAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_bundle_option_grid')->toHtml()
        );       
    }
    
    public function superGroupAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_group')->toHtml()
        );       
    }
    
    public function superConfigAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config_grid')->toHtml()
        );       
    }
    
    protected function _initProduct()
    {
    	$productId  = (int) $this->getRequest()->getParam('id');
        $product    = Mage::getModel('catalog/product')
        	->setStoreId($this->getRequest()->getParam('store', 0));     
        	
        if ($setId = (int) $this->getRequest()->getParam('set')) {
            $product->setAttributeSetId($setId);
        }
        
        if ($typeId = (int) $this->getRequest()->getParam('type'))
        {
        	$product->setTypeId($typeId);	
        }
        
        if ($attributes = $this->getRequest()->getParam('attributes'))
        {
        	$product->setSuperAttributesIds(explode(",", base64_decode(urldecode($attributes))));
        }
        
        if ($productId) {
            $product->load($productId);
        }
               
        Mage::register('product', $product);
    }
    
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        
        try {
            $product = Mage::getModel('catalog/product')
                ->setId($this->getRequest()->getParam('id'))
                ->addData($this->getRequest()->getPost('product'))
                ->validate();
        }
        catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        
        $this->getResponse()->setBody($response->toJson());
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
            $superAttributes = array();
            $superLinks = array();
            
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
            
            if($this->getRequest()->getParam('_super_attributes_json')) {
            	$superAttributes = Zend_Json::decode($this->getRequest()->getParam('_super_attributes_json'));
            }
            
            if($this->getRequest()->getParam('_super_links_json')) {
            	$superLinks = Zend_Json::decode($this->getRequest()->getParam('_super_links_json'));
            }
            
            foreach ($data['product'] as $index=>$value) {
            	if (is_array($value) && $index != 'gallery') {
            	    $data['product'][$index] = implode(',', $value);
            	}
            }
            
        	$product = Mage::getModel('catalog/product')
        		->setStoreId((int) $storeId)
        		->load((int) $this->getRequest()->getParam('id'))
           		->addData($data['product'])
                ->setStoreId((int) $storeId)
                ->setPostedStores($stores)
                ->setPostedCategories($categories)
                ->setRelatedProducts($relatedProducts)
                ->setSuperAttributes($superAttributes)
                ->setSuperLinks($superLinks)
                ->setUpSellProducts($upSellProducts)
                ->setCrossSellProducts($crossSellProducts);
            
            
            if(!$product->getId()) {
	            if ($set = (int) $this->getRequest()->getParam('set')) {
	                $product->setAttributeSetId($set);
	            }
	            
	            if ($type = (int) $this->getRequest()->getParam('type')) {
	                $product->setTypeId($type);
	            }
            }
            
            if($product->isSuperGroup()) {
            	if($this->getRequest()->getPost('_super_group_product')) {
            		$product->setSuperGroupProducts($this->_decodeInput($this->getRequest()->getPost('_super_group_product')));
            	}
            }
                
            if($product->isBundle()) {
            	$options = array();
            	if($optionsJson = $this->getRequest()->getParam('_options_json')) {
            		$options = Zend_Json_Decoder::decode($optionsJson);
            	}

            	$product->setBundleOptions($options);
            }
                       
            try {
                $product->save();
                Mage::getSingleton('adminhtml/session')->addSuccess('Product saved');
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage())
                    ->setProductData($data);
                $this->_redirect('*/*/edit', array('id'=>$product->getId(), 'store'=>$storeId));
                return;
            }
            
            if ($return = $this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id'=>$product->getId(), 'store'=>$product->getStoreId()));
                return;
            }
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
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
