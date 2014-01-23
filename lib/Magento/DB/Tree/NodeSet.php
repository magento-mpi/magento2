<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_DB
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * TODO implements iterators
 *
 */
namespace Magento\DB\Tree;

class NodeSet implements \Iterator
{
    /**
     * @var Node[]
     */
    private $_nodes = array();

    /**
     * @var int
     */
    private $_currentNode = 0;

    /**
     * @var int
     */
    private $_current = 0;

    function __construct() {
        $this->_nodes = array();
        $this->_current = 0;
        $this->_currentNode = 0;
        $this->count = 0;
    }

    /**
     * @param Node $node
     * @return int
     */
    function addNode(Node $node) {
        $this->_nodes[$this->_currentNode] = $node;
        $this->count++;
        return ++$this->_currentNode;
    }

    /**
     * @return int
     */
    function count() {
        return $this->count;
    }

    /**
     * @return bool
     */
    function valid() {
        return  isset($this->_nodes[$this->_current]);
    }

    /**
     * @return bool|int
     */
    function next() {
        if ($this->_current > $this->_currentNode) {
            return false;
        } else {
            return  $this->_current++;
        }
    }

    /**
     * @return int
     */
    function key() {
        return $this->_current;
    }

    /**
     * @return Node
     */
    function current() {
        return $this->_nodes[$this->_current];
    }

    /**
     * @return void
     */
    function rewind() {
        $this->_current = 0;
    }
}
