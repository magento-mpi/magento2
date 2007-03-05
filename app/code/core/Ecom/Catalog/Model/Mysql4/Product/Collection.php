<?php



class Ecom_Catalog_Model_Mysql4_Product_Collection extends Ecom_Core_Model_Collection 
{
    function __construct($config = array())
    {
        parent::__construct(Ecom::getModel('catalog'));

        $productTable   = $this->_dbModel->getTableName('catalog_read', 'product');
        $extensionTable = $this->_dbModel->getTableName('catalog_read', 'product_extension');

        $productColumns = new Zend_Db_Expr("SQL_CALC_FOUND_ROWS $productTable.*");
        $this->_sqlSelect->from($productTable, $productColumns);
        $this->_sqlSelect->join($extensionTable, "$extensionTable.product_id = $productTable.product_id");
        
        $this->setPageSize(9);
        $this->setItemObjectClass('Ecom_Catalog_Model_Mysql4_Product');
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
     * @return  Ecom_Core_Model_Collection
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