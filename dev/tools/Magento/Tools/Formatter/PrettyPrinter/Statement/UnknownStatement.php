<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;

/**
 * This class generically represents the passed in node.
 */
class UnknownStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified node.
     * @param PHPParser_Node $node
     */
    public function __construct(PHPParser_Node $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $this->addToLine(
            $treeNode,
            (new Line('Unknown node: '))->add($this->node->getType())->add(new HardLineBreak())
        );
        return $treeNode;
    }
}
