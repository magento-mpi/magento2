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
    static protected $_read;
    static protected $_write;

    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        self::$_productTable   = Mage::registry('resources')->getTableName('catalog', 'product');
        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_read');
    }
    
    public function load($productId)
    {
        $arrRes = array();
        $productTable = Mage::registry('resources')->getTableName('catalog', 'product');        

        $attributes = $this->getAttributes($productId);
        
        $select = $this->_dbModel->getReadConnection()->select();
        $select->from($productTable);
        
        $multipleAtributes = array();
        foreach ($attributes as $attribute) {

            // Multiples
            if ($attribute['multiple'] && !$withMultipleFields) {
                continue;
            }

            // Prepare join
            $tableCode = 'product_attribute_'.$attribute['data_type'];
            $tableName = Mage::registry('resources')->getTableName('catalog', $tableCode);
            $tableAlias= $tableName . '_' . $attribute['attribute_code'];
            
            $selectTable = $tableName . ' AS ' . $tableAlias;
            $condition = "$tableAlias.product_id=$productTable.product_id
                          AND $tableAlias.attribute_id=".$attribute['attribute_id']."
                          AND $tableAlias.website_id=".Mage_Core_Environment::getCurentWebsite();
            
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
        
        $select->where("$productTable.product_id=$productId");
        
        $arrRes = $this->_dbModel->getReadConnection()->fetchRow($select);

        // Add multiple attributes to result       
        foreach ($multipleAtributes as $attributeCode => $selectParam) {
            $select = $this->_dbModel->getReadConnection()->select();
            $select->from($selectParam['table'] . ' AS ' . $selectParam['alias'], $selectParam['columns']);
            $select->where('product_id='.$productId);
            $select->where('attribute_id='.$selectParam['attribute_id']);
            $select->where('website_id='.Mage_Core_Environment::getCurentWebsite());
            
            if (count($selectParam['columns'])>1) {
                $arrRes[$attributeCode] = $this->_dbModel->getReadConnection()->fetchAll($select);
            }
            else {
                $arrRes[$attributeCode] = $this->_dbModel->getReadConnection()->fetchCol($select);
            }
            
        }
        
        return $arrRes;
    }

    /**
     * Get product attributes
     *
     * @param   int $productId
     * @return  array
     */
    public function getAttributes()
    {
        $productTable       = Mage::registry('resources')->getTableName('catalog', 'product');
        $attributeTable     = Mage::registry('resources')->getTableName('catalog', 'product_attribute');
        $attributeInSetTable= Mage::registry('resources')->getTableName('catalog', 'product_attribute_in_set');
        
        $sql = "SELECT
                    $attributeTable.*
                FROM
                    $productTable,
                    $attributeInSetTable,
                    $attributeTable
                WHERE
                    $productTable.product_id=:product_id
                    AND $attributeInSetTable.product_attribute_set_id=$productTable.attribute_set_id
                    AND $attributeTable.attribute_id=$attributeInSetTable.attribute_id";
        
        $attributes = $this->_dbModel->getReadConnection()->fetchAll($sql, array('product_id'=>$productId));
        return $attributes;
    }
    
    public function getAttributeSetId($productId)
    {
        if (!empty($this->_data['attribute_set_id'])) {
            return $this->_data['attribute_set_id'];
        }
        
        $productTable   = Mage::registry('resources')->getTableName('catalog', 'product');
        $sql = "SELECT
                    attribute_set_id
                FROM
                    $productTable
                WHERE
                    product_id=:product_id";
        return $this->_dbModel->getReadConnection()->fetchOne($sql, array('product_id'=>$productId));
    }
}