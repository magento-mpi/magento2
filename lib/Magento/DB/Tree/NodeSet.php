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
class Magento_DB_Tree_NodeSet implements Iterator
{
    private $_nodes = array();
    private $_currentNode = 0;
    private $_current = 0;


    function __construct() {
        $this->_nodes = array();
        $this->_current = 0;
        $this->_currentNode = 0;
        $this->count = 0;
    }



    function addNode(Magento_DB_Tree_Node $node) {
        $this->_nodes[$this->_currentNode] = $node;
        $this->count++;
        return ++$this->_currentNode;
    }

    function count() {
        return $this->count;
    }


    function valid() {
        return  isset($this->_nodes[$this->_current]);
    }

    function next() {
        if ($this->_current > $this->_currentNode) {
            return false;
        } else {
            return  $this->_current++;
        }
    }

    function key() {
        return $this->_current;
    }


    function current() {
        return $this->_nodes[$this->_current];
    }

    function rewind() {
        $this->_current = 0;
    }
}