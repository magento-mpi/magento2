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
 */
class TreeNode implements NodeInterface
{
    /**
     * This member holds the list of children of this node.
     *
     * @var TreeNode[]
     */
    protected $children;

    /**
     * This member holds the data associated with the node.
     *
     * @var mixed $data User defined data for the node
     */
    protected $data;

    /**
     * This member holds the parent of the current node (i.e. a circular reference).
     *
     * @var NodeInterface
     */
    protected $parent;

    /**
     * This method is used to construct a new tree node with the given data.
     *
     * @param mixed $data User defined data for the node
     */
    public function __construct($data)
    {
        $this->setData($data);
    }

    /**
     * This method adds the named child to the end of the children nodes
     *
     * @param TreeNode $newChild Child node to be added
     * @param TreeNode $adjacentNode Optional child node to place new node next to
     * @param bool $after Flag indicating that the sibling should be added after this node. If false, the sibling is
     * added prior to this node.
     * @return TreeNode
     */
    public function addChild(TreeNode $newChild, TreeNode $adjacentNode = null, $after = true)
    {
        // if adding a child next to an existing child
        if (null !== $adjacentNode) {
            $this->setNodeWithinArray($this->children, $newChild, $adjacentNode, $after);
        } else {
            // otherwise, just add it to the end of the list
            $this->children[] = $newChild;
        }
        // add this node as the parent of the new node
        $newChild->setParent($this);
        // as a convenience, return the newly added node
        return $newChild;
    }

    /**
     * This method adds a sibling node to the current node by adding the new sibling as a child of this nodes parent.
     *
     * @param TreeNode $newSibling Sibling node to be added
     * @param bool $after Flag indicating that the sibling should be added after this node. If false, the sibling is
     * added prior to this node.
     * @return TreeNode
     */
    public function addSibling(TreeNode $newSibling, $after = true)
    {
        if (null !== $this->parent) {
            $this->parent->addChild($newSibling, $this, $after);
        }
        // as a convenience, return the newly added node
        return $newSibling;
    }

    /**
     * This method returns the array of children.
     *
     * @return TreeNode[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * This method returns the data being stored with the node.
     *
     * @return mixed Data being stored with the node.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * This method returns the parent node of the current node.
     *
     * @return TreeNode Node that holds this node as a child.
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * This method returns if this node has children.
     *
     * @return bool Indicator if this node has children.
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * This method removes the specified child from the child list.
     *
     * @param TreeNode $existingChild Node representing an existing child.
     * @return void
     */
    public function removeChild(TreeNode $existingChild)
    {
        $index = array_search($existingChild, $this->children);
        if (false !== $index) {
            unset($this->children[$index]);
            // need to keep the index consistent, so reset to the values
            $this->children = array_values($this->children);
        }
    }

    /**
     * This method sets the data associated with the node.
     *
     * @param mixed $data User defined data for the node
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * This method set the parent node of the current node.
     *
     * @param NodeInterface $parent Node that holds this node as a child.
     * @return void
     */
    public function setParent(NodeInterface $parent)
    {
        // if moving parents, make sure the old parent no longer has a reference
        if (null !== $this->parent) {
            $this->parent->removeChild($this);
        }
        // reset the parent
        $this->parent = $parent;
    }

    /**
     * This method inserts the new node node into the passed in array.
     *
     * @param TreeNode[] &$nodes Array of nodes where the insert is going to take place.
     * @param TreeNode $newNode New node to add to the list.
     * @param TreeNode $adjacentNode Optional node indicating where the new node should be inserted.
     * @param bool $after Flag indicating that the new node should be added after the adjacent node. If false, the
     * new node is added prior to this node.
     * @return void
     */
    public static function setNodeWithinArray(
        array &$nodes,
        TreeNode $newNode,
        TreeNode $adjacentNode = null,
        $after = true
    ) {
        // find the existing child
        $index = array_search($adjacentNode, $nodes, true);
        if (false !== $index) {
            // found it, so splice in the new child
            if ($after) {
                // put the value after the found one if flagged
                $index++;
            }
            // splice in the new node
            array_splice($nodes, $index, 0, array($newNode));
        } elseif (!$after && sizeof($nodes) > 0) {
            // shouldn't really get here, but could; just add it to the start of the list
            array_splice($nodes, 0, 0, array($newNode));
        } else {
            // shouldn't really get here, but could; just add it to the end of the list
            $nodes[] = $newNode;
        }
    }
}
