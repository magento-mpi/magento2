<?php
/**
 * Catalog navigation
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Navigation extends Mage_Core_Block_Template
{
    /**
     * Retrieve category children nodes
     *
     * @param   int $parent
     * @param   int $maxChildLevel
     * @return  Varien_Data_Tree_Node_Collection
     */
    public function getChildCategories($parent, $maxChildLevel=1)
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        $tree->getCategoryCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');
            
        $nodes = $tree->load($parent, $maxChildLevel)
            ->getRoot()
                ->getChildren();

        return $nodes;
    }
    
    /**
     * Retrieve current store categories
     *
     * @param   int $maxChildLevel
     * @return  Varien_Data_Tree_Node_Collection
     */
    public function getStoreCategories($maxChildLevel=1)
    {
        $parent = Mage::getSingleton('core/store')->getConfig('catalog/category/root_id');
        return $this->getChildCategories($parent, $maxChildLevel);
    }
    
    /**
     * Retrieve child categories of current category
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getCurrentChildCategories()
    {
        $parent = $this->getRequest()->getParam('id');
        return $this->getChildCategories($parent, 1);
    }
    
    
    /**
     * Checkin activity of category
     *
     * @param   Varien_Object $category
     * @return  bool
     */
    public function isCategoryActive($category)
    {
        return false;
    }
    
    /**
     * Retrieve category link
     *
     * @param   Varien_Object $category
     * @return  string
     */
    public function getCategoryUrl($category)
    {
        return Mage::getUrl('catalog/category/view', array('id'=>$category->getId()));
    }
}
