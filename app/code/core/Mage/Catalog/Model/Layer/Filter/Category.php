<?php
/**
 * Layer category filter
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Abstract 
{
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'id';
    }
    
    public function apply(Zend_Controller_Request_Abstract $request) 
    {
        return $this;
    }
    
    protected function _initItems()
    {
        $categoty   = Mage::getSingleton('catalog/layer')->getCurrentCategory();
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToSelect('is_anchor')
            ->addIdFilter($categoty->getChildren())
            ->load();
        
        Mage::getSingleton('catalog/layer')->getProductCollection()
            ->addCountToCategories($collection);
            
        $items=array();
        foreach ($collection as $category) {
            if ($category->getProductCount()) {
                $items[] = Mage::getModel('catalog/layer_filter_item')
                    ->setFilter($this)
                    ->setLabel($category->getName())
                    ->setValue($category->getId())
                    ->setCount($category->getProductCount());
            }
        }
        $this->_items = $items;
        return $this;
    }
}
