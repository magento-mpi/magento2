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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Catalog_CategoryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialization category object in registry
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = (int) $this->getRequest()->getParam('id',false);
        
        $storeId    = (int) $this->getRequest()->getParam('store');

        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                    return false;
                }
            }
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
    }
    /**
     * Catalog categories index action
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Add new category form
     */
    public function addAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit category page
     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;

        $storeId = (int) $this->getRequest()->getParam('store');
        $_prevStoreId = Mage::getSingleton('admin/session')
            ->getLastViewedStore(true);

        if ($_prevStoreId != null && !$this->getRequest()->getQuery('isAjax')) {
            $params['store'] = $_prevStoreId;
            $redirect = true;
        }

        $categoryId = (int) $this->getRequest()->getParam('id');
        $_prevCategoryId = Mage::getSingleton('admin/session')
            ->getLastEditedCategory(true);
           
		
        if ($_prevCategoryId && !$this->getRequest()->getQuery('isAjax')) {
           // $params['id'] = $_prevCategoryId;
             $this->getRequest()->setParam('id',$_prevCategoryId);
            //$redirect = true;
        }

         if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }

        if ($storeId && !$categoryId) {
            $store = Mage::app()->getStore($storeId);
            $_prevCategoryId = (int) $store->getRootCategoryId();
            $this->getRequest()->setParam('id', $_prevCategoryId);
        }

        if (!$category = $this->_initCategory()) {
            return;
        }

        if ($this->getRequest()->getQuery('isAjax')) {
            Mage::getSingleton('admin/session')
                ->setLastViewedStore($this->getRequest()->getParam('store'));
            Mage::getSingleton('admin/session')
                ->setLastEditedCategory($category->getId());
            $this->_initLayoutMessages('adminhtml/session');
            $this->getResponse()->setBody(
                $this->getLayout()->getMessagesBlock()->getGroupedHtml().
                $this->getLayout()->createBlock('adminhtml/catalog_category_edit_form')
                    ->toHtml()
            );
            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('catalog/categories');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('catalog-categories');

        $data = Mage::getSingleton('adminhtml/session')->getCategoryData(true);
        if (isset($data['general'])) {
            $category->addData($data['general']);
        }

        $this->_addBreadcrumb(Mage::helper('catalog')->__('Manage Catalog Categories'),
             Mage::helper('catalog')->__('Manage Categories')
        );

        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/catalog_category_tree', 'category.tree')
        );
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/catalog_category_edit')
        );
        $this->renderLayout();
    }

    /**
     * Get tree node (Ajax version)
     */
    public function categoriesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(false);
        }
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/catalog_category_tree')
                    ->getTreeJson($category)
            );
        }
    }

    /**
     * Category save
     */
    public function saveAction()
    {
        if (!$category = $this->_initCategory()) {
            return;
        }

        $storeId = $this->getRequest()->getParam('store');
        if ($data = $this->getRequest()->getPost()) {
            $category->addData($data['general']);

            if (!$category->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    if ($storeId) {
                        $parentId = Mage::app()->getStore($storeId)->getRootCategoryId();
                    }
                    else {
                        $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
                    }
                }
                $parentCategory = Mage::getModel('catalog/category')->load($parentId);
                $category->setPath($parentCategory->getPath());
            }
            /**
             * Check "Use Default Value" checkboxes values
             */
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $category->setData($attributeCode, null);
                }
            }

            $category->setAttributeSetId($category->getDefaultAttributeSetId());

            if (isset($data['category_products'])) {
                $products = array();
                parse_str($data['category_products'], $products);
                $category->setPostedProducts($products);
            }

            try {
              //  if( $this->getRequest()->getParam('image') )

                $category->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('Category saved'));
            }
            catch (Exception $e){
                $this->_getSession()->addError($e->getMessage())
                    ->setCategoryData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=> true, 'id'=>$category->getId())));
                return;
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current'=> true, 'id'=>$category->getId()));

     echo '<script type="text/javascript">parent.updateContent("'.$url.'", {}, true);</script>';

      //  $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=> true, 'id'=>$category->getId())));
    }

    /**
     * Move category tree node action
     */
    public function moveAction()
    {
        $nodeId           = $this->getRequest()->getPost('id', false);
        $parentNodeId     = $this->getRequest()->getPost('pid', false);
        $prevNodeId       = $this->getRequest()->getPost('aid', false);
        $prevParentNodeId = $this->getRequest()->getPost('paid', false);

        try {
            $tree = Mage::getResourceModel('catalog/category_tree')
                ->load($nodeId,$parentNodeId,$prevParentNodeId);
			 
           //Mage::getResourceSingleton('catalog/category')->move($nodeId);
			 
            $node = $tree->getNodeById($nodeId);
            $newParentNode  = $tree->getNodeById($parentNodeId);
            $prevNode       = $tree->getNodeById($prevNodeId);

            if (!$prevNode || !$prevNode->getId()) {
                $prevNode = null;
            }

            $tree->move($node, $newParentNode, $prevNode);

            Mage::dispatchEvent('category_move',
                array(
                    'category_id' => $nodeId,
                    'prev_parent_id' => $prevParentNodeId,
                    'parent_id' => $parentNodeId
            ));

            $this->getResponse()->setBody("SUCCESS");
        }
        catch (Exception $e){
            $this->getResponse()->setBody(Mage::helper('catalog')->__('Category move error'));
        }
    }

    /**
     * Delete category action
     */
    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $category = Mage::getModel('catalog/category')->load($id);
                Mage::dispatchEvent('catalog_controller_category_delete', array('category'=>$category));

                $category->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('Category deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Category delete error'));
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
    }

    public function gridAction()
    {
        if (!$category = $this->_initCategory()) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_category_tab_product')->toHtml()
        );
    }

    public function treeAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $categoryId = (int) $this->getRequest()->getParam('id');

        if ($storeId) {
            if (!$categoryId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = $store->getRootCategoryId();
                $this->getRequest()->setParam('id', $rootId);
            }
        }
        if (!$category = $this->_initCategory()) {
            return;
        }
        $block = $this->getLayout()->createBlock('adminhtml/catalog_category_tree');
        $root = $block->getRoot();
        $response = array();
        $response['data'] = $block->getTree();
        $response['parameters'] = array(
            'text'        => htmlentities($root->getName()),
            'draggable'   => false,
            'allowDrop'   => ($root->getIsVisible())?'true':'false',
            'id'          => (int) $root->getId(),
            'expanded'    => (int) $block->getIsWasExpanded(),
            'store_id'    => (int) $block->getStore()->getId(),
            'category_id' => (int) $category->getId(),
            'root_visible'=> (int) $root->getIsVisible()
        );

        $this->getResponse()->setBody(
            Zend_Json::encode($response)
        );
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('catalog/categories');
    }
}