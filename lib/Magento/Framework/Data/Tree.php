<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\Data;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection as NodeCollection;

/**
 * Data tree
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tree
{
    /**
     * Nodes collection
     *
     * @var NodeCollection
     */
    protected $_nodes;

    /**
     * Enter description here...
     *
     */
    public function __construct()
    {
        $this->_nodes = new NodeCollection($this);
    }

    /**
     * Enter description here...
     *
     * @return \Magento\Framework\Data\Tree
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
    public function load($parentNode = null)
    {
    }

    /**
     * Enter description here...
     *
     * @param int|string $nodeId
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
    public function appendChild($data, $parentNode, $prevNode = null)
    {
        if (is_array($data)) {
            $node = $this->addNode(new Node($data, $parentNode->getIdField(), $this), $parentNode);
        } elseif ($data instanceof Node) {
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
    public function addNode($node, $parent = null)
    {
        $this->_nodes->add($node);
        $node->setParent($parent);
        if (!is_null($parent) && $parent instanceof Node) {
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
    public function moveNodeTo($node, $parentNode, $prevNode = null)
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
    public function copyNodeTo($node, $parentNode, $prevNode = null)
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
    public function createNode($parentNode, $prevNode = null)
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
     * @return NodeCollection
     */
    public function getNodes()
    {
        return $this->_nodes;
    }

    /**
     * Enter description here...
     *
     * @param Node $nodeId
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
        if ($node instanceof Node) {
        } elseif (is_numeric($node)) {
            if ($_node = $this->getNodeById($node)) {
                return $_node->getPath();
            }
        }
        return array();
    }
}
