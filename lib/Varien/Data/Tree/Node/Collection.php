<?php
/**
 * Tree node collection
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Tree_Node_Collection implements ArrayAccess, IteratorAggregate
{
    private $_nodes;
    private $_container;
    
    public function __construct($container) 
    {
        $this->_nodes = array();
        $this->_container = $container;
    }
    
    /**
    * Implementation of IteratorAggregate::getIterator()
    */
    public function getIterator()
    {
        return new ArrayIterator($this->_nodes);
    }

    /**
    * Implementation of ArrayAccess:offsetSet()
    */
    public function offsetSet($key, $value)
    {
        $this->_nodes[$key] = $value;
    }
    
    /**
    * Implementation of ArrayAccess:offsetGet()
    */
    public function offsetGet($key)
    {
        return $this->_nodes[$key];
    }
    
    /**
    * Implementation of ArrayAccess:offsetUnset()
    */
    public function offsetUnset($key)
    {
        unset($this->_nodes[$key]);
    }
    
    /**
    * Implementation of ArrayAccess:offsetExists()
    */
    public function offsetExists($key)
    {
        return isset($this->_nodes[$key]);
    }
    
    /**
    * Adds a node to this node
    */
    public function add(Varien_Data_Tree_Node $node)
    {
        $node->setParent($this->container);

        // Set the Tree for the node
        if ($this->container->getTree() instanceof Varien_Data_Tree) {
            $node->setTree($this->container->getTree());
        }

        $this->_nodes[] = $node;

        return $node;
    }
    
    public function count()
    {
        return count($this->_nodes);
    }
}