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
    
    /**
     * Data tree node constructor
     *
     * @param array $data
     * @param string $idFeild
     * @param Varien_Data_Tree $tree
     * @param Varien_Data_Tree_Node $parent
     */
    public function __construct($data, $idFeild, $tree, $parent = null) 
    {
        $this->setTree($tree);
        $this->setParent($parent);
        $this->setIdField($idFeild);
        $this->setData($data);
        $this->_childNodes = new Varien_Data_Tree_Node_Collection($this);
    }
    
    /**
     * Retrieve node id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getData($this->getIdField());
    }
    
    /**
     * Set node id field name
     *
     * @param   string $idField
     * @return  this
     */
    public function setIdField($idField)
    {
        $this->_idField = $idField;
        return $this;
    }
    
    /**
     * Retrieve node id field name
     *
     * @return string
     */
    public function getIdField()
    {
        return $this->_idField;
    }
    
    /**
     * Set node tree object
     *
     * @param   Varien_Data_Tree $tree
     * @return  this
     */
    public function setTree(Varien_Data_Tree $tree)
    {
        $this->_tree = $tree;
        return $this;
    }
    
    /**
     * Retrieve node tree object
     *
     * @return Varien_Data_Tree
     */
    public function getTree()
    {
        return $this->_tree;
    }
    
    /**
     * Set node parent
     *
     * @param   Varien_Data_Tree_Node $parent
     * @return  Varien_Data_Tree_Node
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
        return $this;
    }
    
    /**
     * Retrieve node parent
     *
     * @return Varien_Data_Tree
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Check node children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_childNodes->count() > 0;
    }
    
    public function isChildOf($node)
    {
        
    }
    
    /**
     * Load node children
     *
     * @param   int  $recursionLevel
     * @return  Varien_Data_Tree_Node
     */
    public function loadChildren($recursionLevel=0)
    {
        $this->_tree->load($this, $recursionLevel);
        return $this;
    }
    
    /**
     * Retrieve node children collection
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getChildren()
    {
        return $this->_childNodes;
    }
    
    public function getLastChild()
    {
        return $this->_childNodes->lastNode();
    }
    
    /**
     * Add child node
     *
     * @param   Varien_Data_Tree_Node $node
     * @return  Varien_Data_Tree_Node
     */
    public function addChild($node)
    {
        $this->_childNodes->add($node);
        return $this;
    }
    
    public function appendChild($prevNode=null)
    {
        $this->_tree->appendChild($this, $prevNode);
        return $this;
    }
    
    public function moveTo($parentNode, $prevNode=null)
    {
        $this->_tree->moveNodeTo($this, $parentNode, $prevNode);
        return $this;
    }
    
    public function copyTo($parentNode, $prevNode=null)
    {
        $this->_tree->copyNodeTo($this, $parentNode, $prevNode);
        return $this;
    }
    
    public function remove()
    {
        $this->_tree->removeNode($this);
        return $this;
    }
}