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
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/catalog/category/tree.phtml');
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
                        'id'=>null
                        ))."')"
                ))
        );
        
        $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher'));
    }
    
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }
    
    public function getCategoryId()
    {
        return Mage::registry('category')->getId();
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
    
    public function getTreeJson()
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        $parentNodeId = (int) $this->getRequest()->getPost('node',1);
        $storeId      = (int) $this->getRequest()->getPost('store',1);
        
        $tree->getCategoryCollection()->addAttributeToSelect('name');
        $root = $tree->load($parentNodeId, 5)
                    ->getRoot();
                        
        $rootArray = $this->_getNodeJson($root);

        $json = Zend_Json::encode($rootArray['children']);
        return $json;
    }
    
    protected function _getNodeJson($node, $level=1)
    {
        $item = array();
        $item['text']= $node->getName(); //.'(id #'.$child->getId().')';
        $item['id']  = $node->getId();
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $item['allowDrop'] = ($level<3) ? true : false;
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
