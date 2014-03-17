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
 *
 * Class NodeVisitorAbstract
 */
class NodeVisitorAbstract implements NodeVisitor
{
    /**
     * This method is called when first visiting a node.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    public function nodeEntry(TreeNode $treeNode)
    {
    }

    /**
     * This method is called when exiting a visited node.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    public function nodeExit(TreeNode $treeNode)
    {
    }
}
