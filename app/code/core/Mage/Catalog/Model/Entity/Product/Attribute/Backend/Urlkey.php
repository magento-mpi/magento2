<?php
/**
 * Product url key attribute backend
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Attribute_Backend_Urlkey extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
    	$attributeName = $this->getAttribute()->getName();
    	
    	$urlKey = $object->getData($attributeName);
    	if ($urlKey=='') {
    		$urlKey = $object->getName();
    	}
    	
		$object->setData($attributeName, $object->formatUrlKey($urlKey));
		
		return $this;
    }

}
