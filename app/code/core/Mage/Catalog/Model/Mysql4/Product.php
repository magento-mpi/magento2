<?php
/**
 * Product model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product extends Mage_Catalog_Model_Product 
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
        parent::__construct($data);
        
        self::$_productTable        = Mage::registry('resources')->getTableName('catalog', 'product');
        self::$_attributeTable      = Mage::registry('resources')->getTableName('catalog', 'product_attribute');
        self::$_attributeInSetTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute_in_set');
        
        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_write');
    }
    
    public function load($productId)
    {
        $arrRes = array();
        $sql = 'SELECT * FROM ' . self::$_productTable . ' WHERE product_id=:product_id';
        $this->setData(self::$_read->fetchRow($sql, array('product_id'=>$productId)));
        
        $attributes = $this->getAttributes();
        
        $select = self::$_read->select();
        $select->from(self::$_productTable);
        
        $multipleAtributes = array();
        foreach ($attributes as $attribute) {

            // Multiples
            if ($attribute['multiple'] && !$this->_useMultipleValues) {
                continue;
            }

            // Prepare join
            $tableCode = 'product_attribute_'.$attribute['data_type'];
            $tableName = Mage::registry('resources')->getTableName('catalog', $tableCode);
            $tableAlias= $tableName . '_' . $attribute['attribute_code'];
            
            $selectTable = $tableName . ' AS ' . $tableAlias;
            $condition = "$tableAlias.product_id=".self::$_productTable.".product_id
                          AND $tableAlias.attribute_id=".$attribute['attribute_id']."
                          AND $tableAlias.website_id=".Mage::registry('website')->getId();
            
            // If data_type==decimal, then is qty field
            if ($attribute['data_type'] == 'decimal') {
                $columns = array(
                    new Zend_Db_Expr("$tableAlias.attribute_value AS " . $attribute['attribute_code']),
                    new Zend_Db_Expr("$tableAlias.attribute_qty AS " . $attribute['attribute_code'] . '_qty'),
                );
            }
            else {
                $columns = array(
                    new Zend_Db_Expr("$tableAlias.attribute_value AS " . $attribute['attribute_code']),
                );
            }

            // Multiples
            if ($attribute['multiple']) {
                $multipleAtributes[$attribute['attribute_code']] = array(
                    'table'     => $tableName,
                    'alias'     => $tableAlias,
                    'columns'   => $columns,
                    'attribute_id' => $attribute['attribute_id']
                );
                continue;
            }
            
            // Join
            if ($attribute['required']) {
                $select->join($selectTable, $condition, $columns);
            }
            else {
                $select->joinLeft($selectTable, $condition, $columns);
            }
        }
        
        $select->where(self::$_productTable.".product_id=$productId");
        
        $arrRes = self::$_read->fetchRow($select);

        // Add multiple attributes to result       
        foreach ($multipleAtributes as $attributeCode => $selectParam) {
            $select = self::$_read->select();
            $select->from($selectParam['table'] . ' AS ' . $selectParam['alias'], $selectParam['columns']);
            $select->where('product_id='.$productId);
            $select->where('attribute_id='.$selectParam['attribute_id']);
            $select->where('website_id='.Mage::registry('website')->getId());
            
            if (count($selectParam['columns'])>1) {
                $arrRes[$attributeCode] = self::$_read->fetchAll($select);
            }
            else {
                $arrRes[$attributeCode] = self::$_read->fetchCol($select);
            }
            
        }
        
        $this->setData($arrRes);
        
        return $this;
    }
}