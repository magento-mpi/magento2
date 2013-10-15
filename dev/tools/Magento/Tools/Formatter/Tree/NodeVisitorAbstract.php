<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\Tree;

/**
 * This class is a base class for visitors so that all methods do not need to be implemented.
 * Class NodeVisitorAbstract
 * @package Magento\Tools\Formatter\Tree
 */
class NodeVisitorAbstract implements NodeVisitor
{
    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        // by default, do nothing
    }

    /**
     * This method is called when exiting a visited node.
     * @param TreeNode $treeNode
     */
    public function nodeExit(TreeNode $treeNode)
    {
        // by default, do nothing
    }
}
