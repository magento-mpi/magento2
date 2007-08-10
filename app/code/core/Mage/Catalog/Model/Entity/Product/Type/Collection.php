<?php
/**
 * Product type collection resource model
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Product_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    public function _construct()
    {
        $this->_init('catalog/product_type');
    }
    
    public function toOptionArray()
    {
    	$options = $this->_toOptionArray('type_id', 'code');
    	    	
    	foreach($options as $key=>$option) {
    		if($option['value']==2) {
    			unset($options[$key]);
    		}
    	}
    	
        return $options;
    }
}