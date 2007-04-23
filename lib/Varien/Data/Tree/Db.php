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
    
    /**
     * Tree ctructure field names
     *
     * @var string
     */
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
        
        $this->_select  = $this->_conn->select();
        $this->_select->from($this->_table, array_values($fields));
        $this->_select->order("$this->_table.$this->_orderField");
    }
    
    public function getDbSelect()
    {
        return $this->_select;
    }
    
    public function setDbSelect($select)
    {
        $this->_select = $select;
    }
    
    /**
     * Load tree
     *
     * @param   int || Varien_Data_Tree_Node $parentNode
     * @param   int $recursionLevel recursion level
     * @return  this
     */
    public function load($parentNode, $recursionLevel=0)
    {
        if ($parentNode instanceof Varien_Data_Tree_Node) {
            $parentId = $parentNode->getId();
        }
        elseif (is_numeric($parentNode)) {
            $parentId = $parentNode;
            $parentNode = null;
        }
        else {
            throw new Exception('root node id is not defined');
        }
        
        $select = clone $this->_select;
        $condition = $this->_conn->quoteInto("$this->_table.$this->_parentField=?", $parentId);
        $select->where($condition);
        
        $arrNodes = $this->_conn->fetchAll($select);
        foreach ($arrNodes as $nodeInfo) {
            $node = new Varien_Data_Tree_Node($nodeInfo, $this->_idField, $this, $parentNode);
            $this->addNode($node, $parentNode);
            
            if ($recursionLevel) {
                $node->loadChildren($recursionLevel-1);
            }
        }
        return $this;
    }
    
    public function loadNode($nodeId)
    {
        $select = clone $this->_select;
        $condition = $this->_conn->quoteInto("$this->_table.$this->_idField=?", $nodeId);
        $select->where($condition);
        
        return new Varien_Data_Tree_Node($this->_conn->fetchRow($select), $this->_idField, $this);
    }
    
    public function appendChild($parentNode, $prevNode=null)
    {
        
    }
    
    /**
     * Move tree node
     *
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     */
    public function moveNodeTo($node, $parentNode, $prevNode=null) 
    {
        $data = array();
        $data[$this->_parentField]  = $parentNode->getId();
        $data[$this->_levelField]   = $parentNode->getData($this->_levelField)+1;
        // New node order
        if (is_null($prevNode) || is_null($prevNode->getData($this->_orderField))) {
            $data[$this->_orderField] = 1;
        }
        else {
            $data[$this->_orderField] = $prevNode->getData($this->_orderField)+1;
        }
        $condition = $this->_conn->quoteInto("$this->_idField=?", $node->getId());
        
        // For reorder new node branch
        $dataReorderNew = array(
            $this->_orderField => new Zend_Db_Expr($this->_conn->quoteIdentifier($this->_orderField).'+1')
        );
        $conditionReorderNew = $this->_conn->quoteIdentifier($this->_parentField).'='.$parentNode->getId().
                            ' AND '.$this->_conn->quoteIdentifier($this->_orderField).'>='. $data[$this->_orderField];

        // For reorder old node branch
        $dataReorderOld = array(
            $this->_orderField => new Zend_Db_Expr($this->_conn->quoteIdentifier($this->_orderField).'-1')
        );
        $conditionReorderOld = $this->_conn->quoteIdentifier($this->_parentField).'='.$node->getData($this->_parentField).
                            ' AND '.$this->_conn->quoteIdentifier($this->_orderField).'>'.$node->getData($this->_orderField);
        
        $this->_conn->beginTransaction();
        try {
            // Prepare new node branch
            $this->_conn->update($this->_table, $dataReorderNew, $conditionReorderNew);
            // Move node
            $this->_conn->update($this->_table, $data, $condition);
            // Update old node branch
            $this->_conn->update($this->_table, $dataReorderOld, $conditionReorderOld);
            $this->_conn->commit();
        }
        catch (Exception $e){
            $this->_conn->rollBack();
            echo $e->getMessage();
        }
        
    }
}