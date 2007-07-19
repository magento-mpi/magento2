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
    
    public function getTreeJson()
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        $parentNodeId = (int) $this->getRequest()->getPost('node',1);
        $storeId = (int) $this->getRequest()->getPost('store',1);
        
        $tree->getCategoryCollection()->addAttributeToSelect('name');
        $nodes = $tree->load($parentNodeId)
                    ->getNodes();

        $items = array();
        foreach ($nodes as $node) {
            $item = array();
            $item['text']= $node->getName(); //.'(id #'.$child->getId().')';
            $item['id']  = $node->getId();
            $item['cls'] = 'folder';
            $item['allowDrop'] = true;
            $item['allowDrag'] = true;
            if (!$node->hasChildren()) {
                $item['leaf'] = 'true';
            }
            $items[] = $item;
        }

        $json = Zend_Json::encode($items);
        return $json;
    }
}
