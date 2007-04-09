<?php

/**
 * Category model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Category extends Mage_Catalog_Model_Category  
{
    public function load($categoryId)
    {
        
    }
    
    public function getAttributes()
    {
        $arrRes = array();
        if ($this->getAttributeSetId()) {
            $attrTable      = Mage::registry('resource')->getTableName('catalog', 'category_attribute');
            $attrInSetTable = Mage::registry('resource')->getTableName('catalog', 'category_attribute_in_set');
            $sql = "SELECT
                        $attrTable.*
                    FROM
                        $attrTable,
                        $attrInSetTable
                    WHERE
                        $attrTable.attribute_id=$attrInSetTable.attribute_id
                        AND $attrInSetTable.category_attribute_set_id=:attr_set";
        }
        return $arrRes;
    }
}