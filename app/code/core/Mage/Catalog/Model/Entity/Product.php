<?php
/**
 * Product entity resource model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Product extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_product')
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );        
    }
    
    protected function _afterSave(Varien_Object $object)
    {
    	foreach($object->getLinkedProductsForSave() as $linkType=>$data) {
    		
	    	$linkedProducts = $object->getLinkedProducts($linkType)->load();
	      	
	       	foreach($data['linkIds'] as $index=>$linkId) {
	       		if(!$linkedProduct = $linkedProducts->getItemByColumnValue('product_id', $linkId)) {
	       			$linkedProduct = clone $linkedProducts->getObject();
	       			$linkedProduct->setAttributeCollection($linkedProducts->getLinkAttributeCollection());
	       			$linkedProduct->addLinkData($linkedProducts->getLinkTypeId(), $object, $linkId);
	       		}
	       		
	   			foreach ($linkedProducts->getLinkAttributeCollection() as $attribute) {
	   				if(isset($data['linkAttributes'][$index][$attribute->getCode()])) {
	   					$linkedProduct->setData($attribute->getCode(), $data['linkAttributes'][$index][$attribute->getCode()]);
	   				}
	   			}
	   					
	   			$linkedProduct->save();
	       	}
	       	
	       	// Now delete unselected items
	       	
	       	foreach($linkedProducts as $linkedProduct) {
				if(!in_array($linkedProduct->getId(), $data['linkIds'])) {
					$linkedProduct->delete();
				}
	       	}
    	}
    	
    	return parent::_afterSave($object);
    }
    
    public function getCategoryCollection($product)
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->joinField('product_id', 
                'catalog/category_product', 
                'product_id', 
                'category_id=entity_id', 
                null)
            ->addFieldToFilter('product_id', (int) $product->getId())
            ->load();
        return $collection;
    }
}
