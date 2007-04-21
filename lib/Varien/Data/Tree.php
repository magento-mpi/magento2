<?php
/**
 * Data tree
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Tree
{
    /**
     * Nodes collection
     *
     * @var Varien_Data_Tree_Node_Collection
     */
    protected $_nodes;
    
    public function __construct() 
    {
        $this->_nodes = new Varien_Data_Tree_Node_Collection($this);
    }
    
    public function getTree()
    {
        return $this;
    }
    
    public function load($rootId, $recurcive=false)
    {
        
    }
    
    public function addNode($node, $parent=null)
    {
        $this->_nodes->add($node);
        if ($parent instanceof Varien_Data_Tree_Node) {
            $parent->add($node);
        }
    }
    public function moveNodeTo();
    public function copyNodeTo();
    public function removeNode();
    public function createNode();
    
    public function getPath();
    public function getChild();
    public function getChildren();
}