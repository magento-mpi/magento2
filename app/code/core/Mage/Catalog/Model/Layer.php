<?php
/**
 * Catalog view layer model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Layer extends Varien_Object
{
    /**
     * Retrieve current layer product collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getProductCollection()
    {
        $collection = $this->getData('product_collection');
        if (is_null($collection)) {
            $collection = $this->getCurrentCategory()->getProductCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('description')
                ->joinField('store_id', 
                    'catalog/product_store', 
                    'store_id', 
                    'product_id=entity_id', 
                    '{{table}}.store_id='.(int) $this->getCurrentStore()->getId());
                    
            if ($this->getCurrentCategory()->getIsAnchor()) {
                $categoryCondition = '{{table}}.category_id in ('.$this->getCurrentCategory()->getAllChildren().')';
                $collection->getSelect()->distinct(true);
            }
            else {
                $categoryCondition = '{{table}}.category_id='.(int) $this->getCurrentCategory()->getId();
            }
            $collection->joinField('position', 
                    'catalog/category_product', 
                    'position', 
                    'product_id=entity_id', 
                    $categoryCondition);
            
            $collection->getEntity()->setStore((int) $this->getCurrentStore()->getId());
            $this->setData('product_collection', $collection);
        }
        
        return $collection;
    }
    
    /**
     * Retrieve current category model
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        $category = $this->getData('current_category');
        if (is_null($category)) {
            if ($category = Mage::registry('current_category')) {
                $this->setData('current_category', $category);
            }
            else {
                Mage::throwException('Can not retrieve current category object');
            }
        }
        return $category;
    }
    
    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::getSingleton('core/store');
    }
    
    public function getFilterableAttributes()
    {
        $entity = $this->getProductCollection()->getEntity();
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getConfig()->getId())
            ->addIsFilterableFilter()
            ->load();
        foreach ($collection as $item) {
        	$item->setEntity($entity);
        }
        
        return $collection;
    }
}
