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
    protected $_rootNode;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/category/tree.phtml');
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
        return $this->getCategory()->getId();
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
    
    public function getRootNode()
    {
        if (!$this->_rootNode) {
            $tree = $this->getCategory()->getTreeModel();
            $storeId = (int) $this->getRequest()->getParam('store');
            
            if ($storeId) {
                $store = Mage::getModel('core/store')->load($storeId);
                $parentNodeId = (int) $store->getConfig('catalog/category/root_id');
            }
            else {
                $parentNodeId = 1;
            }
            
            $tree->getCategoryCollection()->addAttributeToSelect('name')
                ->getEntity()
                    ->setStore(0);
            $this->_rootNode = $tree->load($parentNodeId, 5)
                ->getRoot()
                ->setIsVisible($parentNodeId!=1);
        }
        return $this->_rootNode;
    }
    
    public function getTreeJson()
    {
                        
        $rootArray = $this->_getNodeJson($this->getRootNode());
        $json = Zend_Json::encode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }
    
    protected function _getNodeJson($node, $level=1)
    {
        $item = array();
        $item['text']= $node->getName(); //.'(id #'.$child->getId().')';
        $item['id']  = $node->getId();
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $item['allowDrop'] = true;
        $item['allowDrag'] = true;
        if ($node->hasChildren()) {
            $item['children'] = array();
            foreach ($node->getChildren() as $child) {
            	$item['children'][] = $this->_getNodeJson($child, $level+1);
            }
        }
        else {
            $item['leaf'] = 'true';
        }
        $items[] = $item;
        return $item;
    }
}
