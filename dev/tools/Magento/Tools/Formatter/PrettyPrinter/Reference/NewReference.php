<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_New;

class NewReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified new reference.
     * @param PHPParser_Node_Expr_New $node
     */
    public function __construct(PHPParser_Node_Expr_New $node)
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
        // add in the new statement
        $line->add('new ');
        // add in the class reference
        $this->resolveNode($this->node->class, $treeNode);
        // add in the arguments
        $line->add('(');
        $this->processArgumentList($this->node->args, $treeNode, $line, new CallLineBreak());
        $line->add(')');
    }
}
