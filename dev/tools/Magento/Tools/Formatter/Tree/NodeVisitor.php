<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\Tree;

/**
 * This interface is used to implement visitors to tree nodes. Mulitple calls are made on entry and exit such that
 * parent calls surround its children.
 * Class NodeVisitor
 * @package Magento\Tools\Formatter\Tree
 */
interface NodeVisitor
{
    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode);

    /**
     * This method is called when exiting a visited node.
     * @param TreeNode $treeNode
     */
    public function nodeExit(TreeNode $treeNode);
}
