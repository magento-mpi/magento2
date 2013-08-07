<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Tree node collection
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Tree_Node_Collection implements ArrayAccess, IteratorAggregate
{
    private $_nodes;
    private $_container;
    
    public function __construct($container) 
    {
        $this->_nodes = array();
        $this->_container = $container;
    }
    
    public function getNodes()
    {
        return $this->_nodes;
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
    public function add(Magento_Data_Tree_Node $node)
    {
        $node->setParent($this->_container);

        // Set the Tree for the node
        if ($this->_container->getTree() instanceof Magento_Data_Tree) {
            $node->setTree($this->_container->getTree());
        }

        $this->_nodes[$node->getId()] = $node;

        return $node;
    }
    
    public function delete($node)
    {
        if (isset($this->_nodes[$node->getId()])) {
            unset($this->_nodes[$node->getId()]);
        }
        return $this;
    }
    
    public function count()
    {
        return count($this->_nodes);
    }

    public function lastNode()
    {
        return !empty($this->_nodes) ? $this->_nodes[count($this->_nodes) - 1] : null;
    }

    public function searchById($nodeId)
    {
        if (isset($this->_nodes[$nodeId])) {
            return $this->_nodes[$nodeId];
        }
        return null;
    }
}
