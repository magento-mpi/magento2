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
class Magento_Data_Tree_Node extends Magento_Object
{
    /**
     * Parent node
     *
     * @var Magento_Data_Tree_Node
     */
    protected $_parent;

    /**
     * Main tree object
     *
     * @var Magento_Data_Tree
     */
    protected $_tree;

    /**
     * Child nodes
     *
     * @var Magento_Data_Tree_Node_Collection
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
     * @param Magento_Data_Tree $tree
     * @param Magento_Data_Tree_Node $parent
     */
    public function __construct($data, $idFeild, $tree, $parent = null)
    {
        $this->setTree($tree);
        $this->setParent($parent);
        $this->setIdField($idFeild);
        $this->setData($data);
        $this->_childNodes = new Magento_Data_Tree_Node_Collection($this);
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
     * @param   Magento_Data_Tree $tree
     * @return  this
     */
    public function setTree(Magento_Data_Tree $tree)
    {
        $this->_tree = $tree;
        return $this;
    }

    /**
     * Retrieve node tree object
     *
     * @return Magento_Data_Tree
     */
    public function getTree()
    {
        return $this->_tree;
    }

    /**
     * Set node parent
     *
     * @param   Magento_Data_Tree_Node $parent
     * @return  Magento_Data_Tree_Node
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
        return $this;
    }

    /**
     * Retrieve node parent
     *
     * @return Magento_Data_Tree
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

    public function setLevel($level)
    {
        $this->setData('level', $level);
        return $this;
    }

    public function setPathId($path)
    {
        $this->setData('path_id', $path);
        return $this;
    }

    public function isChildOf($node)
    {

    }

    /**
     * Load node children
     *
     * @param   int  $recursionLevel
     * @return  Magento_Data_Tree_Node
     */
    public function loadChildren($recursionLevel=0)
    {
        $this->_tree->load($this, $recursionLevel);
        return $this;
    }

    /**
     * Retrieve node children collection
     *
     * @return Magento_Data_Tree_Node_Collection
     */
    public function getChildren()
    {
        return $this->_childNodes;
    }

    public function getAllChildNodes(&$nodes = array())
    {
        foreach ($this->_childNodes as $node) {
        	$nodes[$node->getId()] = $node;
        	$node->getAllChildNodes($nodes);
        }
        return $nodes;
    }

    public function getLastChild()
    {
        return $this->_childNodes->lastNode();
    }

    /**
     * Add child node
     *
     * @param   Magento_Data_Tree_Node $node
     * @return  Magento_Data_Tree_Node
     */
    public function addChild($node)
    {
        $this->_childNodes->add($node);
        return $this;
    }

    public function appendChild($prevNode=null)
    {
        $this->_tree->appendChild($this, $prevNode);
        return $this;
    }

    public function moveTo($parentNode, $prevNode=null)
    {
        $this->_tree->moveNodeTo($this, $parentNode, $prevNode);
        return $this;
    }

    public function copyTo($parentNode, $prevNode=null)
    {
        $this->_tree->copyNodeTo($this, $parentNode, $prevNode);
        return $this;
    }

    public function removeChild($childNode)
    {
        $this->_childNodes->delete($childNode);
        return $this;
    }

    public function getPath(&$prevNodes = array())
    {
        if ($this->_parent) {
            array_push($prevNodes, $this);
            $this->_parent->getPath($prevNodes);
        }
        return $prevNodes;
    }

    public function getIsActive()
    {
        return $this->_getData('is_active');
    }

    public function getName()
    {
        return $this->_getData('name');
    }

}