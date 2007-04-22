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
    
    /**
     * Node ID field name
     *
     * @var string
     */
    protected $_idField;
    
    public function __construct($data, $idFeild, $tree, $parent = null) 
    {
        $this->_tree    = $tree;
        $this->_parent  = $parent;
        $this->_idField = $idFeild;
        $this->setData($data);
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
        return $this;
    }
    
    public function getTree()
    {
        return $this->_tree;
    }
    
    public function setParent($parent)
    {
        $this->_parent = $parent;
        return $this;
    }
    
    public function getParent()
    {
        return $this->_parent;
    }
    
    public function hasChildren()
    {
        return $this->_childNodes->count() > 0;
    }
    
    public function loadChildren($recursionLevel=0)
    {
        $this->_tree->load($this, $recursionLevel);
        return $this;
    }
    
    public function getChildren()
    {
        return $this->_childNodes;
    }
    
    public function addChild($node)
    {
        $this->_childNodes->add($node);
    }
    
    public function appendChild($prevNode=null)
    {
        $this->_tree->appendChild($this, $prevNode);
    }
    
    public function moveTo($node)
    {
        
    }
    
    public function copyTo($node)
    {
        
    }
}