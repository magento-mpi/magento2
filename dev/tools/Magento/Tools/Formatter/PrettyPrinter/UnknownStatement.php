<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class generically represents the passed in node.
 */
class UnknownStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_NodeAbstract $node
     */
    public function __construct(\PHPParser_NodeAbstract $node)
    {
        parent::__construct($node);
    }

    /**
     * This method is used to process the current node.
     *
     * @param Tree $tree
     */
    public function process(Tree $tree)
    {
        $tree->addRoot(new TreeNode('Unknown node: ' . $this->node->getType()));
    }
}
