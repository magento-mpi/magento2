<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\NodeVisitorAbstract;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class is used as a base class to keep track of node levels while traversing a tree.
 * Class LevelNodeVisitor
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
abstract class LevelNodeVisitor extends NodeVisitorAbstract
{
    /**
     * This member holds the current level of traversal (i.e. how many indents are needed).
     * @var int
     */
    protected $level = -1;

    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        // entering the a new node, so new level
        $this->level++;
    }

    /**
     * This method is called when exiting a visited node.
     * @param TreeNode $treeNode
     */
    public function nodeExit(TreeNode $treeNode)
    {
        // leaving a the now, so going back to the parent
        $this->level--;
    }
}
