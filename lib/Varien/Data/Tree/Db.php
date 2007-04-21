<?php
/**
 * Data DB tree
 * 
 * Data model:
 * id  |  pid  |  level | order
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Tree_Db extends Varien_Data_Tree 
{
    const ID_FIELD      = 'id';
    const PARENT_FIELD  = 'parent';
    const LEVEL_FIELD   = 'level';
    const ORDER_FIELD   = 'order';
    
    /**
     * DB connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_conn;
    
    /**
     * Data table name
     *
     * @var string
     */
    protected $_table;
    
    /**
     * SQL select object
     *
     * @var Zend_Db_Select
     */
    protected $_select;
    
    protected $_idField;
    protected $_parentField;
    protected $_levelField;
    protected $_orderField;
    
    /**
     * Db tree constructor
     * 
     * $fields = array(
     *      Varien_Data_Tree_Db::ID_FIELD       => string,
     *      Varien_Data_Tree_Db::PARENT_FIELD   => string,
     *      Varien_Data_Tree_Db::LEVEL_FIELD    => string
     *      Varien_Data_Tree_Db::ORDER_FIELD    => string
     * )
     * 
     * @param Zend_Db_Adapter_Abstract $connection
     * @param string $table
     * @param array $fields
     */
    public function __construct($connection, $table, $fields) 
    {
        parent::__construct();
        
        $this->_conn    = $connection;
        $this->_table   = $table;
        $this->_select  = $this->_conn->select();
        
        if (!isset($fields[self::ID_FIELD]) || 
            !isset($fields[self::PARENT_FIELD]) || 
            !isset($fields[self::LEVEL_FIELD]) || 
            !isset($fields[self::ORDER_FIELD])) {
                
            throw new Exception('"$fields" tree configuratin array');
        }
        
        $this->_idField     = $fields[self::ID_FIELD];
        $this->_parentField = $fields[self::PARENT_FIELD];
        $this->_levelField  = $fields[self::LEVEL_FIELD];
        $this->_orderField  = $fields[self::ORDER_FIELD];
    }
    
    public function getDbSelect()
    {
        return $this->_select;
    }
    
    public function setDbSelect($select)
    {
        $this->_select = $select;
    }
}