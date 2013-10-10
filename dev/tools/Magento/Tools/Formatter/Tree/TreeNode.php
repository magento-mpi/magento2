<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\Tree;

/**
 * This class is a generic implementation of a tree node.
 * Class TreeNode
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
class TreeNode {
    /**
     * This member holds the list of children of this node.
     * @var array
     */
    protected $children;

    /**
     * This member holds the data associated with the node.
     * @param mixed $data User defined data for the node
     */
    protected $data;

    /**
     * This method is used to construct a new tree node with the given data.
     * @param mixed $data User defined data for the node
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * This method adds the named child to the end of the children nodes
     * @param TreeNode $treeNode Child node to be added
     */
    public function addChild(TreeNode $treeNode) {
        $this->children[] = $treeNode;
    }

    /**
     * This method returns the array of children.
     * @return array
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * This method returns the data being stored with the node.
     * @return mixed Data being stored with the node.
     */
    public function getData() {
        return $this->data;
    }

    /**
     * This method returns if this node has children.
     * @return bool Indicator if this node has children.
     */
    public function hasChildren() {
        return count($this->children) > 0;
    }
}