<?php
/**
 * Catalog product link model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Product_Link extends Mage_Core_Model_Abstract
{
	protected $_attributeCollection = null;
	
	protected function _construct()
	{
		$this->_init('catalog/product_link');
	}
	
	public function getDataForSave() 
	{
		$data = array();
		$data['product_id'] = $this->getProductId();
		$data['linked_product_id'] = $this->getLinkedProductId();
		$data['link_type_id'] = $this->getLinkTypeId();
		return $data;
	}
	
	public function getAttributeCollection()
	{
		if(is_null($this->_attributeCollection)) {
			$this->setAttibuteCollection(
				Mage::getResourceModel('catalog/product_link_attribute_collection')
					->addFieldToFilter('link_type_id', $this->getLinkTypeId())
					->load()
			);
		}
		
		return $this->_attributeCollection;
	}
	
	public function setAttributeCollection($collection)
	{
		$this->_attributeCollection = $collection;
		return $this;
	}
		
}// Class Mage_Catalog_Model_Product_Link END
