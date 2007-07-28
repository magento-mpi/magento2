<?php
/**
 * Catalog product link attributes collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Link_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_filterAlias = array();
	
	protected function _construct()
	{
		$this->_init('catalog/product_link_attribute');
	}
	
	public function addLinkTypeData()
	{
		$this->getSelect()->join(array('link_type'=>$this->getTable('product_link_type')), 
    			    			 'main_table.link_type_id = link_type.link_type_id', array('code AS link_type'));
    	$this->_filterAlias['link_type'] = 'link_type.code';
	}
	
	public function getItemByCodeAndLinkType($code, $linkType=null)
	{
		foreach ($this->getItems() as $item) {
			if ($item->getCode() == $code && is_null($linkType)) {
				return $item;
			} elseif ($item->getCode() == $code && $item->getLinkType() == $linkType) {
				return $item;
			}
		}
							
		return false;
	}
	
	public function addFieldToFilter($field, $condition) {
		if(isset($this->_filterAlias[$field])) {
			$field = $this->_filterAlias[$field];
		}
		
		parent::addFieldToFilter($field, $condition);
		
		return $this;
	}
	
}// Class Mage_Catalog_Model_Entity_Product_Link_Attribute_Collection END