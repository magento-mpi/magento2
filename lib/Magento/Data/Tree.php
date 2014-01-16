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
 * Data tree
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data;

use \Magento\Data\Tree\Node;

class Tree
{

    /**
     * Nodes collection
     *
     * @var \Magento\Data\Tree\Node\Collection
     */
    protected $_nodes;

    /**
     * Enter description here...
     *
     */
    public function __construct()
    {
        $this->_nodes = new \Magento\Data\Tree\Node\Collection($this);
    }

    /**
     * Enter description here...
     *
     * @return \Magento\Data\Tree
     */
    public function getTree()
    {
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Node $parentNode
     * @return void
     */
    public function load($parentNode=null)
    {
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $nodeId
     * @return void
     */
    public function loadNode($nodeId)
    {
    }

    /**
     * Append child
     *
     * @param array|Node $data
     * @param Node $parentNode
     * @param Node $prevNode
     * @return Node
     */
    public function appendChild($data, $parentNode, $prevNode=null)
    {
        if (is_array($data)) {
            $node = $this->addNode(
                new \Magento\Data\Tree\Node($data, $parentNode->getIdField(), $this),
                $parentNode
            );
        } elseif ($data instanceof \Magento\Data\Tree\Node) {
            $node = $this->addNode($data, $parentNode);
        }
        return $node;
    }

    /**
     * Add node
     *
     * @param Node $node
     * @param Node $parent
     * @return Node
     */
    public function addNode($node, $parent=null)
    {
        $this->_nodes->add($node);
        $node->setParent($parent);
        if (!is_null($parent) && ($parent instanceof \Magento\Data\Tree\Node) ) {
            $parent->addChild($node);
        }
        return $node;
    }

    /**
     * Move node
     *
     * @param Node $node
     * @param Node $parentNode
     * @param Node $prevNode
     * @return void
     */
    public function moveNodeTo($node, $parentNode, $prevNode=null)
    {
    }

    /**
     * Copy node
     *
     * @param Node $node
     * @param Node $parentNode
     * @param Node $prevNode
     * @return void
     */
    public function copyNodeTo($node, $parentNode, $prevNode=null)
    {
    }

    /**
     * Remove node
     *
     * @param Node $node
     * @return $this
     */
    public function removeNode($node)
    {
        $this->_nodes->delete($node);
        if ($node->getParent()) {
            $node->getParent()->removeChild($node);
        }
        unset($node);
        return $this;
    }

    /**
     * Create node
     *
     * @param Node $parentNode
     * @param Node $prevNode
     * @return void
     */
    public function createNode($parentNode, $prevNode=null)
    {
    }

    /**
     * Get child
     *
     * @param Node $node
     * @return void
     */
    public function getChild($node)
    {
    }

    /**
     * Get children
     *
     * @param Node $node
     * @return void
     */
    public function getChildren($node)
    {
    }

    /**
     * Enter description here...
     *
     * @return \Magento\Data\Tree\Node\Collection
     */
    public function getNodes()
    {
        return $this->_nodes;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $nodeId
     * @return Node
     */
    public function getNodeById($nodeId)
    {
        return $this->_nodes->searchById($nodeId);
    }

    /**
     * Get path
     *
     * @param Node $node
     * @return array
     */
    public function getPath($node)
    {
        if ($node instanceof \Magento\Data\Tree\Node ) {

        } elseif (is_numeric($node)){
            if ($_node = $this->getNodeById($node)) {
                return $_node->getPath();
            }
        }
        return array();
    }

}
