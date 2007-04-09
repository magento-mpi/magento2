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
    /**
     * These made static to avoid saving in object
     *
     * @var string
     */
    static protected $_categoryTable;
    static protected $_attributeTable;
    static protected $_attributeValueTable;
    static protected $_read;
    static protected $_write;
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        self::$_categoryTable   = Mage::registry('resources')->getTableName('catalog', 'category');
        self::$_attributeTable  = Mage::registry('resources')->getTableName('catalog', 'category_attribute');
        self::$_attributeValueTable  = Mage::registry('resources')->getTableName('catalog', 'category_attribute_value');
        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_read');
    }

    public function load($categoryId)
    {
        $sql = 'SELECT
                    *
                FROM
                    '.self::$_categoryTable.'
                WHERE
                    category_id=:category_id';
        $this->setData(self::$_read->fetchRow($sql, array('category_id'=>$categoryId)));
        
        $attributes = $this->getAttributes();
        
        if (!empty($attributes)) {
            $select = self::$_read->select();
            $select->from(self::$_categoryTable);

            foreach ($attributes as $index => $attribute) {
                // Prepare join
                $tableAlias= self::$_attributeValueTable . '_' . $attribute['attribute_code'];
                
                $selectTable = self::$_attributeValueTable . ' AS ' . $tableAlias;
                $condition = "$tableAlias.category_id=".self::$_categoryTable.".category_id
                              AND $tableAlias.attribute_id=".$attribute['attribute_id']."
                              AND $tableAlias.website_id=".Mage::registry('website')->getId();
                
                $columns = array(new Zend_Db_Expr("$tableAlias.attribute_value AS " . $attribute['attribute_code']));

    
                $select->joinLeft($selectTable, $condition, $columns);
            }
            $select->where(self::$_categoryTable . ".category_id=$categoryId");
        }
        $this->setData(self::$_read->fetchRow($select));
    }
}