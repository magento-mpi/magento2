<?php
/**
 * Categories tree block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tree extends Mage_Core_Block_Template 
{
    protected $_withProductCount;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/category/tree.phtml');
        $this->_withProductCount = true;
    }
    
    protected function _initChildren()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Add New'),
                    'onclick'   => "setLocation('".Mage::getUrl('*/*/add', array(
                        '_current'=>true, 
                        'parent'=>$this->getCategoryId(),
                        'id'=>null,
                        ))."')",
                    'class' => 'add'
                ))
        );
        
        $this->setChild('store_switcher', 
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setSwitchUrl(Mage::getUrl('*/*/*', array('store'=>null)))
        );
    }
    
    protected function _getDefaultStoreId()
    {
        return 0;
    }
    
    public function getCategoryCollection($storeId=null)
    {
        if (is_null($storeId)) {
            $storeId = $this->_getDefaultStoreId();
        }
        
        $collection = $this->getData('category_collection_'.$storeId);
        if (is_null($collection)) {
            $collection = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('name')
                ->setLoadProductCount($this->_withProductCount)
                ->setProductStoreId($this->getRequest()->getParam('store', $this->_getDefaultStoreId()));
            $collection->getEntity()
                ->setStore($storeId);
            $this->setData('category_collection_'.$storeId, $collection);
        }
        return $collection;
    }
    
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }
    
    public function getCategory()
    {
        return Mage::registry('category');
    }
    
    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
        return 1;
    }
    
    public function getNodesUrl()
    {
        return $this->getUrl('*/catalog_category/jsonTree');
    }
    
    public function getEditUrl()
    {
        return $this->getUrl('*/catalog_category/edit', array('_current'=>true, 'id'=>null, 'parent'=>null));
    }
    
    public function getMoveUrl()
    {
        return $this->getUrl('*/catalog_category/move');
    }
    
    public function getRoot()
    {
        $root = $this->getData('root');
        if (is_null($root)) {
            $storeId = (int) $this->getRequest()->getParam('store');
            
            if ($storeId) {
                $store = Mage::getModel('core/store')->load($storeId);
                $rootId = (int) $store->getConfig('catalog/category/root_id');
            }
            else {
                $rootId = 1;
            }

            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->load();
                
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != 1) {
                $root->setIsVisible(true);
            }
            elseif($root && $root->getId() == 1) {
                $root->setName(__('Root'));
            }
            
            $this->_addCategoryInfo($root);
            $this->setData('root', $root);
        }
        
        return $root;
    }
    
    public function getTreeJson()
    {
        $rootArray = $this->_getNodeJson($this->getRoot());
        $json = Zend_Json::encode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }
    
    protected function _addCategoryInfo($node)
    {
        if ($node) {
            $children = $node->getAllChildNodes();
            
            $children[$node->getId()] = $node;
            
            $collection = $this->getCategoryCollection()
                ->addIdFilter(array_keys($children))
                ->load();
            foreach ($collection as $category) {
            	$children[$category->getId()]->addData($category->getData());
            }
        }
        
        return $this;
    }
    
    protected function _getNodeJson($node, $level=0)
    {
        $item = array();
        $item['text']= $node->getName();
        if ($this->_withProductCount) {
             $item['text'].= ' ('.$node->getProductCount().')';
        } 
        $item['id']  = $node->getId();
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $item['allowDrop'] = true;
        $item['allowDrag'] = ($node->getLevel()==1) ? false : true;
        if ($node->hasChildren()) {
            $item['children'] = array();
            foreach ($node->getChildren() as $child) {
            	$item['children'][] = $this->_getNodeJson($child, $level+1);
            }
        }
        return $item;
    }
}
