<?php



class Mage_Catalog_Model_Mysql4_Product_Collection extends Mage_Core_Model_Collection 
{
    function __construct($config = array())
    {
        parent::__construct(Mage::getModel('catalog'));

        $productTable   = $this->_dbModel->getTableName('catalog_read', 'product');
        $varcharTable = $this->_dbModel->getTableName('catalog_read', 'product_attribute_varchar');
        $textTable = $this->_dbModel->getTableName('catalog_read', 'product_attribute_text');
        $decimalTable = $this->_dbModel->getTableName('catalog_read', 'product_attribute_decimal');
        $intTable = $this->_dbModel->getTableName('catalog_read', 'product_attribute_int');

        $productColumns = new Zend_Db_Expr("SQL_CALC_FOUND_ROWS $productTable.*");
        $this->_sqlSelect->from($productTable, $productColumns);
        $this->_sqlSelect->join($varcharTable, "$varcharTable.product_id = $productTable.product_id");
        
        $this->setPageSize(9);
        $this->setItemObjectClass('Mage_Catalog_Model_Mysql4_Product');
    }
    
    function addSearchFilter($query)
    {
        $query = trim(strip_tags($query));
        if (!empty($query)) {
            $condition = $this->_dbModel->getReadConnection()->quoteInto("(name LIKE ? OR description LIKE ?)", "%$query%");
            $this->addFilter('search', $condition, 'string');
        }
        return $this;
    }

    /**
     * Set select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Mage_Core_Model_Collection
     */
    public function setOrder($field, $direction = 'desc')
    {
        if ($field == 'product_id') {
        	$field = $this->_dbModel->getTableName('catalog_read', 'product').'.'.$field;
        }
    	return parent::setOrder($field, $direction);
    }
        
    /**
     * Get sql for get record count
     *
     * @return  string
     */
    public function getSelectCountSql()
    {
    	return 'SELECT FOUND_ROWS()';
    }
}