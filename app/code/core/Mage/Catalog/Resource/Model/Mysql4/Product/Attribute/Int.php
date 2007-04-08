<?php
/**
 * Product int attribute model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Resource_Model_Mysql4_Product_Attribute_Int extends Mage_Catalog_Resource_Model_Mysql4_Product_Attribute_Abstract
{
    public function __construct() 
    {
        $this->_attributeValueTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute_int');
    }
}