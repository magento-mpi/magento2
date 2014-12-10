<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\NodeVisitorAbstract;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class is used as a base class to keep track of node levels while traversing a tree.
 * Class LevelNodeVisitor
 */
abstract class LevelNodeVisitor extends NodeVisitorAbstract
{
    /**
     * This member holds the current level of traversal (i.e. how many indents are needed).
     *
     * @var int
     */
    protected $level;

    /**
     * This method constructs a new visitor with the given starting level.
     *
     * @param int $level Starting level for the traversal.
     */
    public function __construct($level = -1)
    {
        $this->level = $level;
    }

    /**
     * This method is called when first visiting a node.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        // entering the a new node, so new level
        $this->level++;
    }

    /**
     * This method is called when exiting a visited node.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    public function nodeExit(TreeNode $treeNode)
    {
        // leaving a the now, so going back to the parent
        $this->level--;
    }
}
