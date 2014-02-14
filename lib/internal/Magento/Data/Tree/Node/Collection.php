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
namespace Magento\Data\Tree\Node;

use Magento\Data\Tree;
use Magento\Data\Tree\Node;

class Collection implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array
     */
    private $_nodes;

    /**
     * @var Node
     */
    private $_container;

    /**
     * @param Node $container
     */
    public function __construct($container) 
    {
        $this->_nodes = array();
        $this->_container = $container;
    }

    /**
     * Get the nodes
     *
     * @return array
     */
    public function getNodes()
    {
        return $this->_nodes;
    }
    
    /**
     * Implementation of \IteratorAggregate::getIterator()
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_nodes);
    }

    /**
     * Implementation of \ArrayAccess:offsetSet()
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->_nodes[$key] = $value;
    }
    
    /**
     * Implementation of \ArrayAccess:offsetGet()
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->_nodes[$key];
    }
    
    /**
     * Implementation of \ArrayAccess:offsetUnset()
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->_nodes[$key]);
    }
    
    /**
     * Implementation of \ArrayAccess:offsetExists()
     * @param string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->_nodes[$key]);
    }
    
    /**
     * Adds a node to this node
     * @param Node $node
     * @return Node
     */
    public function add(Node $node)
    {
        $node->setParent($this->_container);

        // Set the Tree for the node
        if ($this->_container->getTree() instanceof Tree) {
            $node->setTree($this->_container->getTree());
        }

        $this->_nodes[$node->getId()] = $node;

        return $node;
    }

    /**
     * Delete
     *
     * @param Node $node
     * @return $this
     */
    public function delete($node)
    {
        if (isset($this->_nodes[$node->getId()])) {
            unset($this->_nodes[$node->getId()]);
        }
        return $this;
    }

    /**
     * Return count
     *
     * @return int
     */
    public function count()
    {
        return count($this->_nodes);
    }

    /**
     * Return the last node
     *
     * @return mixed
     */
    public function lastNode()
    {
        return !empty($this->_nodes) ? $this->_nodes[count($this->_nodes) - 1] : null;
    }

    /**
     * Search by Id
     *
     * @param string $nodeId
     * @return null
     */
    public function searchById($nodeId)
    {
        if (isset($this->_nodes[$nodeId])) {
            return $this->_nodes[$nodeId];
        }
        return null;
    }
}
