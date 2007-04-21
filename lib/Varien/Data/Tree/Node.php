<?php
/**
 * Data tree node
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Tree_Node extends Varien_Data_Object 
{
    /**
     * Parent node
     *
     * @var Varien_Data_Tree_Node
     */
    protected $_parent;
    
    /**
     * Main tree object
     *
     * @var Varien_Data_Tree
     */
    protected $_tree;
    
    /**
     * Child nodes
     *
     * @var Varien_Data_Tree_Node_Collection
     */
    protected $_childNodes;
    
    protected $_idField;
    
    public function __construct($tree, $parent = null) 
    {
        $this->_tree    = $tree;
        $this->_parent  = $parent;
        $this->_childNodes = new Varien_Data_Tree_Node_Collection($this);
    }
    
    public function getId()
    {
        return $this->getData($this->getIdField());
    }
    
    public function setIdField($idField)
    {
        $this->_idField = $idField;
    }
    
    public function getIdField()
    {
        return $this->_idField;
    }
    
    public function setTree($tree)
    {
        $this->_tree = $tree;
    }
    
    public function getTree()
    {
        return $this->_tree;
    }
    
    public function setParent($parent)
    {
        $this->_parent = $parent;
    }
    
    public function getParent()
    {
        return $this->_parent;
    }
    
    public function hasChildren()
    {
        return $this->_childNodes->count() > 0;
    }
    
    public function moveTo($node)
    {
        
    }
    
    public function copyTo($node)
    {
        
    }
}