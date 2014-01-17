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
 * Data tree node
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Tree;

use Magento\Data\Tree;
use Magento\Data\Tree\Node\Collection;

class Node extends \Magento\Object
{
    /**
     * Parent node
     *
     * @var Node
     */
    protected $_parent;

    /**
     * Main tree object
     *
     * @var Tree
     */
    protected $_tree;

    /**
     * Child nodes
     *
     * @var Collection
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
     * @param Tree $tree
     * @param Node $parent
     */
    public function __construct($data, $idFeild, $tree, $parent = null)
    {
        $this->setTree($tree);
        $this->setParent($parent);
        $this->setIdField($idFeild);
        $this->setData($data);
        $this->_childNodes = new \Magento\Data\Tree\Node\Collection($this);
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
     * @param   Tree $tree
     * @return  this
     */
    public function setTree(\Magento\Data\Tree $tree)
    {
        $this->_tree = $tree;
        return $this;
    }

    /**
     * Retrieve node tree object
     *
     * @return Tree
     */
    public function getTree()
    {
        return $this->_tree;
    }

    /**
     * Set node parent
     *
     * @param   Node $parent
     * @return  $this
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
        return $this;
    }

    /**
     * Retrieve node parent
     *
     * @return Tree
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

    /**
     * @param mixed $level
     *
     * @return $this
     */
    public function setLevel($level)
    {
        $this->setData('level', $level);
        return $this;
    }

    /**
     * @param mixed $path
     *
     * @return $this
     */
    public function setPathId($path)
    {
        $this->setData('path_id', $path);
        return $this;
    }

    /**
     * @param Node $node
     *
     * @return void
     */
    public function isChildOf($node)
    {

    }

    /**
     * Load node children
     *
     * @param   int  $recursionLevel
     * @return  \Magento\Data\Tree\Node
     */
    public function loadChildren($recursionLevel=0)
    {
        $this->_tree->load($this, $recursionLevel);
        return $this;
    }

    /**
     * Retrieve node children collection
     *
     * @return \Magento\Data\Tree\Node\Collection
     */
    public function getChildren()
    {
        return $this->_childNodes;
    }

    /**
     * @param array $nodes
     * @return array
     */
    public function getAllChildNodes(&$nodes = array())
    {
        foreach ($this->_childNodes as $node) {
        	$nodes[$node->getId()] = $node;
        	$node->getAllChildNodes($nodes);
        }
        return $nodes;
    }

    /**
     * @return mixed
     */
    public function getLastChild()
    {
        return $this->_childNodes->lastNode();
    }

    /**
     * Add child node
     *
     * @param   Node $node
     * @return  Node
     */
    public function addChild($node)
    {
        $this->_childNodes->add($node);
        return $this;
    }

    /**
     * @param Node $prevNode
     * @return $this
     */
    public function appendChild($prevNode=null)
    {
        $this->_tree->appendChild($this, $prevNode);
        return $this;
    }

    /**
     * @param Node $parentNode
     * @param Node $prevNode
     * @return $this
     */
    public function moveTo($parentNode, $prevNode=null)
    {
        $this->_tree->moveNodeTo($this, $parentNode, $prevNode);
        return $this;
    }

    /**
     * @param Node $parentNode
     * @param Node $prevNode
     * @return $this
     */
    public function copyTo($parentNode, $prevNode=null)
    {
        $this->_tree->copyNodeTo($this, $parentNode, $prevNode);
        return $this;
    }

    /**
     * @param Node $childNode
     * @return $this
     */
    public function removeChild($childNode)
    {
        $this->_childNodes->delete($childNode);
        return $this;
    }

    /**
     * @param array $prevNodes
     * @return array
     */
    public function getPath(&$prevNodes = array())
    {
        if ($this->_parent) {
            $prevNodes[] = $this;
            $this->_parent->getPath($prevNodes);
        }
        return $prevNodes;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->_getData('is_active');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_getData('name');
    }

}
