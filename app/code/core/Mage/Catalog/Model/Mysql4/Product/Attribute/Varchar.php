<?php
/**
 * Product date attribute model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Varchar extends Mage_Catalog_Model_Mysql4_Product_Attribute_Abstract
{
    public function __construct() 
    {
        $this->_attributeValueTable = $this->getTableName('catalog_setup', 'product_attribute_varchar');
    }
}