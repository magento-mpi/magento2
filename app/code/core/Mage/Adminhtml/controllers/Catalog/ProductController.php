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
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Mage_Catalog');
    }

    /**
     * Initialize product from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
    	$productId  = (int) $this->getRequest()->getParam('id');
        $product    = Mage::getModel('catalog/product')
        	->setStoreId($this->getRequest()->getParam('store', 0));

        if ($setId = (int) $this->getRequest()->getParam('set')) {
            $product->setAttributeSetId($setId);
        }

        if ($typeId = (int) $this->getRequest()->getParam('type')) {
        	$product->setTypeId($typeId);
        }

        if ($attributes = $this->getRequest()->getParam('attributes')) {
        	$product->setSuperAttributesIds(explode(",", base64_decode(urldecode($attributes))));
        }

        if ($productId) {
            $product->load($productId);
        }

        Mage::register('product', $product);
        Mage::register('current_product', $product);
        return $product;
    }

    /**
     * Product list page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/products');

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/catalog_product')
        );

        $this->renderLayout();
    }

    /**
     * Create new product page
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Product edit form
     */
    public function editAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/products');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);
        $product = $this->_initProduct();

        if ($product->getId()) {
            $this->_addLeft(
                $this->getLayout()->createBlock('adminhtml/store_switcher')
                    ->setDefaultStoreName($this->__('Default Values'))
                    ->setWebsiteIds($product->getWebsiteIds())
                    ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'store'=>null)))
            );
        }

        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_edit'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/catalog_product_edit_tabs', 'product_tabs'));
        $this->_addJs($this->getLayout()->createBlock('core/template')->setTemplate('catalog/product/js.phtml'));

        $this->renderLayout();
    }

    /**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_grid')->toHtml()
        );
    }

    /**
     * Related products grid for AJAX request
     */
    public function relatedAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_related')->toHtml()
        );
    }

    /**
     * Upsell products grid for AJAX request
     */
    public function upsellAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_upsell')->toHtml()
        );
    }

    /**
     * Creosssell products grid for AJAX request
     */
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

    /**
     * Initialize product before saving
     */
    protected function _initProductSave()
    {
        $product    = $this->_initProduct();
        $product->addData($this->getRequest()->getPost('product'));

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $product->setData($attributeCode, null);
            }
        }


//        $categories = array();
//        $stores     = array();
//        $relatedProducts    = array();
//        $upSellProducts     = array();
//        $crossSellProducts  = array();
//        $superAttributes    = array();
//        $superLinks         = array();
//
//        if(isset($data['categories'])) {
//            $categories = explode(',', $data['categories']);
//        }
//
//        if (isset($data['stores'])) {
//            $stores = $data['stores'];
//        }
//
//        if($this->getRequest()->getPost('_related_products')) {
//        	$relatedProducts = $this->_decodeInput($this->getRequest()->getPost('_related_products'));
//        }
//
//        if($this->getRequest()->getPost('_up_sell_products')) {
//        	$upSellProducts = $this->_decodeInput($this->getRequest()->getPost('_up_sell_products'));
//        }
//
//        if($this->getRequest()->getPost('_cross_sell_products')) {
//        	$crossSellProducts = $this->_decodeInput($this->getRequest()->getPost('_cross_sell_products'));
//        }
//
//        if($this->getRequest()->getParam('_super_attributes_json')) {
//        	$superAttributes = Zend_Json::decode($this->getRequest()->getParam('_super_attributes_json'));
//        }
//
//        if($this->getRequest()->getParam('_super_links_json')) {
//        	$superLinks = Zend_Json::decode($this->getRequest()->getParam('_super_links_json'));
//        }
//
//        $product->setStoreId((int) $storeId)
//       		->addData($data['product'])
//            ->setPostedStores($stores)
//            ->setPostedCategories($categories)
//            ->setRelatedProducts($relatedProducts)
//            ->setSuperAttributes($superAttributes)
//            ->setSuperLinks($superLinks)
//            ->setUpSellProducts($upSellProducts)
//            ->setCrossSellProducts($crossSellProducts);
//
//        if($product->isSuperGroup()) {
//        	if($this->getRequest()->getPost('_super_group_product')) {
//        		$product->setSuperGroupProducts($this->_decodeInput($this->getRequest()->getPost('_super_group_product')));
//        	}
//        }
//
//        if($product->isBundle()) {
//        	$options = array();
//        	if($optionsJson = $this->getRequest()->getParam('_options_json')) {
//        		$options = Zend_Json_Decoder::decode($optionsJson);
//        	}
//
//        	$product->setBundleOptions($options);
//        }

        return $product;
    }

    /**
     * Save product action
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');

        if ($data = $this->getRequest()->getPost()) {
            $product = $this->_initProductSave();

            try {
                $product->save();
                $productId = $product->getId();

                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess($this->__('Product was successfully saved.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            }
            catch (Exception $e) {
                echo $e;
                $this->_getSession()->addException($e, $this->__('Product saving error.'));
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
                'set'   => $this->getRequest()->getParam('set'),
                'type'  => $this->getRequest()->getParam('type'),
                'store' => $this->getRequest()->getParam('store'),
                'tab'   => $this->getRequest()->getParam('tab'),
                'attributes' => $this->getRequest()->getParam('attributes'),
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    public function duplicateAction()
    {
        $productId = (int) $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')
            ->load($productId);
        try {
            $product->copy();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Product duplicated'));
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
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Product deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
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
        $alertModel = Mage::getSingleton('customeralert/config')->getAlertByType($alertType);
        $alertModel->setParamValues($this->getRequest()->getParams());
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_alerts_customers',$alertType,array('id'=>$alertType))
                ->setModel($alertModel)
                ->loadCustomers()
                ->toHtml()
        );
    }

    public function addCustomersToAlertQueueAction()
    {
        $alerts = Mage::getSingleton('customeralert/config')->getAlerts();;
        $block = $this->getLayout()
            ->createBlock('core/messages', 'messages');
        $collection = $block
            ->getMessageCollection();
        foreach ($alerts as $key=>$val) {
            try {
                if(Mage::getSingleton('customeralert/config')->getAlertByType($key)
                    ->setParamValues($this->getRequest()->getParams())
                    ->addCustomersToAlertQueue())
                {
                    $collection->addMessage(Mage::getModel('core/message')->success($this->__('Customers for alert %s was successfuly added to queue', Mage::getSingleton('customeralert/config')->getTitleByType($key))));
                }
            } catch (Exception $e) {
                $collection->addMessage(Mage::getModel('core/message')->error($this->__('Error while adding customers for %s alert. Message: %s',Mage::getSingleton('customeralert/config')->getTitleByType($key),$e->getMessage())));
                continue;
            }
        }
        print $block->getGroupedHtml();
        return $this;
    }

    public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if(!is_array($productIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select product(s)'));
        } else {
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    Mage::dispatchEvent('catalog_controller_product_delete', array('product'=>$product));
                    $product->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) were successfully deleted', count($productIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if(!is_array($productIds)) {
            // No products selected
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select product(s)'));
        } else {
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($storeId)
                        ->load($productId)
                        ->setStoreId($storeId)
                        ->setStatus($this->getRequest()->getParam('status'));

                     // Workaround for store dissapearing bug
                     $storeIds = $product->getResource()->getStoreIds($product);
                     $storeIds = array_fill_keys($storeIds, $storeIds);
                     if ($product->getStoreId() == 0) {
                        $product->setPostedStores($storeIds);
                     } else {
                        $product->setPostedStores(array($product->getStoreId()=>$product->getStoreId()));
                     }

                     $product->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($productIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/', array('store'=>(int)$this->getRequest()->getParam('store', 0)));
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
