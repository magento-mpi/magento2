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
    
    public function load($parentNode=null, $recursive=false) {}
    public function loadNode($nodeId) {}
    public function appendChild($data=array(), $parentNode, $prevNode=null) 
    {
        if (is_array($data)) {
            $node = $this->addNode(
                new Varien_Data_Tree_Node($data, $parentNode->getIdField(), $this),
                $parentNode
            );
        }
        elseif ($data instanceof Varien_Data_Tree_Node) {
            $node = $this->addNode($data, $parentNode);
        }
        return $node;
    }
    
    public function addNode($node, $parent=null)
    {
        $this->_nodes->add($node);
        $node->setParent($parent);
        if (!is_null($parent) && ($parent instanceof Varien_Data_Tree_Node) ) {
            $parent->addChild($node);
        }
        return $node;
    }
    
    public function moveNodeTo($node, $parentNode, $prevNode=null) {}
    public function copyNodeTo($node, $parentNode, $prevNode=null) {}
    
    public function removeNode($node) 
    {
        $this->_nodes->delete($node);
        if ($node->getParent()) {
            $node->getParent()->removeChild($node);
        }
        unset($node);
        return $this;
    }
    
    public function createNode($parentNode, $prevNode=null) {}
    
    public function getChild($node) {}
    public function getChildren($node) {}

    public function getNodes()
    {
        return $this->_nodes;
    }
    
    public function getNodeById($nodeId)
    {
        return $this->_nodes->searchById($nodeId);
    }

    public function getPath($node)
    {
        if ($node instanceof Varien_Data_Tree_Node ) {
            
        }
        elseif (is_numeric($node)){
            if ($_node = $this->getNodeById($node)) {
                return $_node->getPath();
            }
        }
        return array();
    }
}