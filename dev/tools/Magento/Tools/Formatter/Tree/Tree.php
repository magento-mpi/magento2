<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\Tree;

/**
 * This class is used to hold the source representation in tree format. Child in the trees represent indentation levels.
 * Class Tree
 */
class Tree implements NodeInterface
{
    /**
     * This member holds the root(s) of the tree.
     *
     * @var TreeNode[]
     */
    protected $rootNode;

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
        // defer to adding a new root
        $this->addRoot($newChild, $adjacentNode, $after);
        // as a convenience, return the newly added node
        return $newChild;
    }

    /**
     * This method makes the passed in node a root node. If there is no root node, the new node will be the root. If
     * there are existing roots, an array of roots will be generated.
     *
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
                $this->rootNode = [$this->rootNode, $treeNode];
            } else {
                $this->rootNode = [$treeNode, $this->rootNode];
            }
        }
        // make the tree itself the parent of the new root
        $treeNode->setParent($this);
        // as a convenience, return the newly added node
        return $treeNode;
    }

    /**
     * This method adds a sibling node to the roots nodes.
     *
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
     *
     * @return void
     */
    public function clear()
    {
        $this->rootNode = null;
    }

    /**
     * This method returns the array of children. For the tree, the children are the roots..
     *
     * @return TreeNode[]
     */
    public function getChildren()
    {
        return $this->rootNode;
    }

    /**
     * This method removes the specified child from the child list.
     *
     * @param TreeNode $existingChild Node representing an existing child.
     * @return void
     */
    public function removeChild(TreeNode $existingChild)
    {
        $index = array_search($existingChild, $this->rootNode);
        if (false !== $index) {
            unset($this->rootNode[$index]);
            // need to keep the index consistent, so reset to the values
            $this->rootNode = array_values($this->rootNode);
        }
    }

    /**
     * This method traverses the tree and allows the passed in visitor to visit every node in the tree.
     *
     * @param NodeVisitorInterface $visitor Instance doing the visiting.
     * @return void
     */
    public function traverse(NodeVisitorInterface $visitor)
    {
        if (null !== $this->rootNode) {
            if (is_array($this->rootNode)) {
                // loop through the roots by index since the traversal may cause additional nodes to
                // be added or removed
                $index = 0;
                while ($index < sizeof($this->rootNode)) {
                    $this->traverseNode($this->rootNode[$index], $visitor);
                    $index++;
                }
            } else {
                $this->traverseNode($this->rootNode, $visitor);
            }
        }
    }

    /**
     * This method visits the passed in node and recursively calls the method to process all the children.
     *
     * @param TreeNode $treeNode Node to traversed.
     * @param NodeVisitorInterface $visitor Instance doing the visiting.
     * @return void
     */
    protected function traverseNode(TreeNode $treeNode, NodeVisitorInterface $visitor)
    {
        // call the visitor for the current node
        $visitor->nodeEntry($treeNode);
        // recursively call this method for all the children
        if ($treeNode->hasChildren()) {
            // loop through the children by index since the traversal may cause additional nodes to
            // be added or removed
            $index = 0;
            while ($index < sizeof($treeNode->getChildren())) {
                $this->traverseNode($treeNode->getChildren()[$index], $visitor);
                $index++;
            }
        }
        // call the visitor for the current node
        $visitor->nodeExit($treeNode);
    }
}
