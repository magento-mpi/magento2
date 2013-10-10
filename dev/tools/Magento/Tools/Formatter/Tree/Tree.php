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
class Tree {
    /**
     * This member holds the current node of the tree, which can be interpreted as the last added node.
     * @var TreeNode
     */
    protected $currentNode;

    /**
     * This member holds the root of the tree.
     * @var TreeNode
     */
    protected $rootNode;

    /**
     * This method adds the passed in node as a child in the current node. If the root is not set, it is added as the
     * root.
     * @param TreeNode $treeNode Node to be added as a the child of the current node.
     * @param bool $setCurrent Flag indicating if the newly added node should be made current
     */
    public function addChild(TreeNode $treeNode, $setCurrent = true) {
        // if no node, then add this as the root
        if (null === $this->rootNode || null === $this->currentNode) {
            $this->addRoot($treeNode);
        } else {
            // otherwise, add it as a child of the current node
            $this->currentNode->addChild($treeNode);
        }
        // make the new node the current node
        if ($setCurrent) {
            $this->currentNode = $treeNode;
        }
    }

    /**
     * This method clears the contents of the tree.
     */
    public function clear() {
        $this->rootNode = null;
        $this->currentNode = null;
    }

    /**
     * This method traverses the tree and allows the passed in visitor to visit every node in the tree.
     * @param NodeVisitor $visitor Instance doing the visiting.
     */
    public function traverse(NodeVisitor $visitor) {
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
     * This method makes the passed in node a root node. If there is no root node, the new node will be the root. If
     * there are existing roots, an array of roots will be generated.
     * @param TreeNode $treeNode Node to be added as a root.
     */
    protected function addRoot(TreeNode $treeNode) {
        // if no root, make the new node the root
        if (null === $this->rootNode) {
            $this->rootNode = $treeNode;
        } elseif (is_array($this->rootNode)) {
            // if an array, then just add the node to the end of the roots
            $this->rootNode[] = $treeNode;
        }
        else {
            // only a single root, so make it an array
            $this->rootNode = array($this->rootNode, $treeNode);
        }
    }

    /**
     * This method visits the passed in node and recursively calls the method to process all the children.
     * @param TreeNode $treeNode Node to traversed.
     * @param NodeVisitor $visitor Instance doing the visiting.
     */
    protected function traverseNode(TreeNode $treeNode, NodeVisitor $visitor) {
        // call the visitor for the current node
        $visitor->nodeEntry($treeNode);
        // recursively call this method for all the children
        if ($treeNode->hasChildren()) {
            foreach ($treeNode->getChildren() as $child) {
                $this->traverseNode($child, $visitor);
            }
        }
        // call the visitor for the current node
        $visitor->nodeExit($treeNode);
    }
}