<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_ClosureUse;

class ClosureUseReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified expression.
     * @param PHPParser_Node_Expr_ClosureUse $node
     */
    public function __construct(PHPParser_Node_Expr_ClosureUse $node)
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
        /*
            public function pExpr_ClosureUse(PHPParser_Node_Expr_ClosureUse $node) {
            return ($node->byRef ? '&' : '') . '$' . $node->var;}
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        if ($this->node->byRef) {
            $line->add('&');
        }
        $line->add('$')->add($this->node->var);
    }
}
