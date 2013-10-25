<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
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
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node $node
     */
    public function __construct(PHPParser_Node $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $line->add((new Line('Unknown node: '))->add($this->node->getType())->add(new HardLineBreak()));
    }
}
