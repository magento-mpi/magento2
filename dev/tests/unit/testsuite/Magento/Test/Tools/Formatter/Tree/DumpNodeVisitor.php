<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Formatter\Tree;

use Magento\Tools\Formatter\Tree\NodeVisitorAbstract;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class is used to dump information about the node.
 * Class DumpNodeVisitor
 */
class DumpNodeVisitor extends NodeVisitorAbstract
{
    public $prefix;

    public $result = '';

    public function nodeEntry(TreeNode $treeNode)
    {
        $this->result .= $this->prefix . $treeNode->getData() . PHP_EOL;
        $this->prefix .= '.';
    }

    /**
     * @param TreeNode $treeNode
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function nodeExit(TreeNode $treeNode)
    {
        $this->prefix = substr($this->prefix, 0, strlen($this->prefix) - 1);
    }
}
