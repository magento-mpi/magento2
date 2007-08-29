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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layer category filter
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Abstract 
{
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'cat';
    }
    
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) 
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        $category = Mage::getModel('catalog/category')->load($filter);
        
        if ($this->_isValidCategory($category)) {
            $this->getLayer()->getProductCollection()
                ->addCategoryFilter($category, true);
            
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($category->getName(), $filter)
            );
            $this->_items = array();
        }
        
        return $this;
    }
    
    protected function _isValidCategory($category)
    {
        return $category->getId();
    }
    
    public function getName()
    {
        return __('Category');
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
            if ($category->getIsActive() && $category->getProductCount()) {
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
