<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
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
        $this->loadLayout();
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
                    ->setDefaultStoreName(__('Default Values'))
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
        	$productId      = (int) $this->getRequest()->getParam('id');
        	$attributeSetId = (int) $this->getRequest()->getParam('set');
        	$typeId         = (int) $this->getRequest()->getParam('type');

        	if (!$productId && (!$attributeSetId || !$typeId)) {
        	    // error
        	}

        	$product = Mage::getModel('catalog/product');
        	if (!$productId) {
        	    $product->setAttributeSetId($attributeSetId);
        	    $product->setTypeId($typeId);
        	}
        	else {
        	    $product->load($productId);
        	}

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

            $product->setStoreId((int) $storeId)
           		->addData($data['product'])
                ->setStoreId((int) $storeId)
                ->setPostedStores($stores)
                ->setPostedCategories($categories)
                ->setRelatedProducts($relatedProducts)
                ->setSuperAttributes($superAttributes)
                ->setSuperLinks($superLinks)
                ->setUpSellProducts($upSellProducts)
                ->setCrossSellProducts($crossSellProducts);


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
                if ($product->getStoresChangedFlag()) {
                     Mage::dispatchEvent('catalog_controller_product_save_visibility_changed', array('product'=>$product));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(__('Product saved'));
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

        $this->_redirect('*/*/', array('store'=>$storeId));
    }

    public function duplicateAction()
    {
        $productId = (int) $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')
            ->load($productId);
        try {
            $product->copy();
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Product duplicated'));
            $this->_redirect('*/*/edit', array('_current'=>true, 'id'=>$product->getId()));
        }
        catch (Exception $e){
            Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('_current'=>true));
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
                Mage::dispatchEvent('catalog_controller_product_delete', array('product'=>$product));
                $product->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Product deleted'));
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

    public function tagGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_tag', 'admin.product.tags')
                ->setProductId($this->getRequest()->getParam('id'))
                ->toHtml()
        );
    }
    
    public function alertsGridAction()
    {
        $alertType = $this->getRequest()->getParam('type');
        $alertModel = Mage::getModel('customeralert/config')->getAlertByType($alertType);
        $alertModel->addData(array(
            'product_id' => $this->getRequest()->getParam('product_id'),
            'store_id' => $this->getRequest()->getParam('store_id'),
        ));
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_alerts_customers',$alertType,array('id'=>$alertType))
                ->setModel($alertModel)   
                ->loadCustomers()
                ->toHtml()
        );
    }
    
    public function addCustomersToAlertQueueAction()
    {
        $alert = Mage::getModel('customeralert/config')->getAlerts();
        foreach ($alert as $key=>$val) {
            Mage::getModel('customeralert/config')->getAlertByType($key)
                ->addCustomersToAlertQueue();
        }
    }

    public function tagCustomerGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_tag_customer', 'admin.product.tags.customers')
                ->setProductId($this->getRequest()->getParam('id'))
                ->toHtml()
        );
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('catalog/products');
    }
}
