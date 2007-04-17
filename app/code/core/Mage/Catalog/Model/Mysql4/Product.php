<?php
/**
 * Product model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product
{
    /**
     * These made static to avoid saving in object
     *
     * @var string
     */
    static protected $_productTable;
    static protected $_attributeTable;
    static protected $_attributeInSetTable;
    
    static protected $_read;
    static protected $_write;

    public function __construct($data=array()) 
    {
        self::$_productTable        = Mage::registry('resources')->getTableName('catalog_resource', 'product');
        self::$_attributeTable      = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
        self::$_attributeInSetTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_in_set');
        
        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_write');
    }
    
    public function load($productId)
    {
        $productId = (int) $productId;
        $arr = array();
        
        $sql = 'SELECT * FROM ' . self::$_productTable . ' WHERE product_id=:product_id';
        $baseInfo = self::$_read->fetchRow($sql, array('product_id'=>$productId));
        if (empty($baseInfo)) {
            return $arr;
        }
        
        $select = self::$_read->select();
        $select->from(self::$_productTable);
        
        $attributes = $this->getAttributes($baseInfo['attribute_set_id']);

        foreach ($attributes as $attribute) {
            // Prepare join
            $tableName = $attribute->getTableName();
            $tableAlias= $attribute->getTableAlias();
            
            $condition = "$tableAlias.product_id=".self::$_productTable.".product_id
                          AND $tableAlias.attribute_id=".$attribute->getId()."
                          AND $tableAlias.website_id=".Mage::registry('website')->getId();
            
            // Join
            if ($attribute->isRequired()) {
                $select->join($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
            }
            else {
                $select->joinLeft($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
            }
        }
        
        $select->where(self::$_productTable.".product_id=:product_id");
        
        $arr = self::$_read->fetchRow($select, array('product_id'=>$productId));

        // Add multiple attributes to result       
        $multipleAtributes = $attributes->getMultiples();
        foreach ($multipleAtributes as $attribute) {
            $select = self::$_read->select();
            $select->from($attribute->getSelectTable(), $attribute->getTableColumns())
                ->where('product_id='.$productId)
                ->where('attribute_id='.$attribute->getId())
                ->where('website_id='.Mage::registry('website')->getId());
            
            if (count($attribute->getTableColumns())>1) {
                $arrRes[$attribute->getCode()] = self::$_read->fetchAll($select);
            }
            else {
                $arrRes[$attribute->getCode()] = self::$_read->fetchCol($select);
            }
        }
        
        return $arr;
    }
    
    public function save()
    {
        
    }
    
    /**
     * Get product attributes collection
     *
     * @param   int $productId
     * @return  array
     */
    public function getAttributes($attributeSetId)
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_collection')
            ->addSetFilter($attributeSetId)
            ->load();
        return $collection;
    }
}