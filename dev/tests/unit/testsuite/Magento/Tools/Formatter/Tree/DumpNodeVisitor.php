<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\Tree;


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
