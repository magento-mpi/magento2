<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\Tree;

/**
 * This class is used to hold the source representation in tree format. Child in the trees represent indentation levels.
 * Class Tree
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
class Tree implements Node
{
    /**
     * This member holds the root(s) of the tree.
     */
    protected $rootNode;

    /**
     * This method adds the named child to the end of the children nodes
     * @param TreeNode $newChild Child node to be added
     * @param TreeNode $adjacentNode Optional child node to place new node next to
     * @param bool $after Flag indicating that the sibling should be added after this node. If false, the sibling is
     * added prior to this node.
     * @return TreeNode
     */
    public function addChild(TreeNode $newChild, TreeNode $adjacentNode = null, $after = true)
    {
        // defer to adding a new root
        $this->addRoot($newChild, $adjacentNode, $after);
        // as a convenience, return the newly added node
        return $newChild;
    }

    /**
     * This method makes the passed in node a root node. If there is no root node, the new node will be the root. If
     * there are existing roots, an array of roots will be generated.
     * @param TreeNode $treeNode Node to be added as a root.
     * @param TreeNode $adjacentNode Optional root node to place new node next to
     * @param bool $after Flag indicating that the sibling should be added after this node. If false, the sibling is
     * added prior to this node.
     * @return TreeNode
     */
    public function addRoot(TreeNode $treeNode, TreeNode $adjacentNode = null, $after = true)
    {
        // if no root, make the new node the root
        if (null === $this->rootNode) {
            $this->rootNode = $treeNode;
        } elseif (is_array($this->rootNode)) {
            // if an array, then just splice the new node into the array
            TreeNode::setNodeWithinArray($this->rootNode, $treeNode, $adjacentNode, $after);
        } else {
            // only a single root, so make it an array
            if ($after) {
                $this->rootNode = array($this->rootNode, $treeNode);
            } else {
                $this->rootNode = array($treeNode, $this->rootNode);
            }
        }
        // make the tree itself the parent of the new root
        $treeNode->setParent($this);
        // as a convenience, return the newly added node
        return $treeNode;
    }

    /**
     * This method adds a sibling node to the roots nodes.
     * @param TreeNode $newSibling Sibling node to be added
     * @param bool $after Flag indicating that the sibling should be added after this node. If false, the sibling is
     * added prior to this node.
     * @return TreeNode
     */
    public function addSibling(TreeNode $newSibling, $after = true)
    {
        // defer to adding a new root
        $this->addRoot($newSibling);
        // as a convenience, return the newly added node
        return $newSibling;
    }

    /**
     * This method clears the contents of the tree.
     */
    public function clear()
    {
        $this->rootNode = null;
    }

    /**
     * This method traverses the tree and allows the passed in visitor to visit every node in the tree.
     * @param NodeVisitor $visitor Instance doing the visiting.
     */
    public function traverse(NodeVisitor $visitor)
    {
        if (null !== $this->rootNode) {
            if (is_array($this->rootNode)) {
                foreach ($this->rootNode as $rootNode) {
                    $this->traverseNode($rootNode, $visitor);
                }
            } else {
                $this->traverseNode($this->rootNode, $visitor);
            }
        }
    }

    /**
     * This method visits the passed in node and recursively calls the method to process all the children.
     * @param TreeNode $treeNode Node to traversed.
     * @param NodeVisitor $visitor Instance doing the visiting.
     */
    protected function traverseNode(TreeNode $treeNode, NodeVisitor $visitor)
    {
        // call the visitor for the current node
        $visitor->nodeEntry($treeNode);
        // recursively call this method for all the children
        if ($treeNode->hasChildren()) {
            // loop through the children by index since the traversal may cause additional nodes to
            // be added or removed
            $index = 0;
            while ($index < sizeof($treeNode->getChildren())) {
                $child = $treeNode->getChildren()[$index];
                $this->traverseNode($child, $visitor);
                $index++;
            }
        }
        // call the visitor for the current node
        $visitor->nodeExit($treeNode);
    }
}
