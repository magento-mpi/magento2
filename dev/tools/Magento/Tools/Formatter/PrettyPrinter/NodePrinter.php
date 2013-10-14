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

class NodePrinter extends NodeVisitorAbstract
{
    /**
     * This member holds what is being used as a prefix to the line (i.e. 4 spaces).
     */
    const PREFIX = '    ';

    /**
     * This member holds the result of the traversal.
     * @var string
     */
    public $result = '';

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

        $line = $treeNode->getData();

        $this->result .= $line->getLine(str_repeat(self::PREFIX, $this->level));
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