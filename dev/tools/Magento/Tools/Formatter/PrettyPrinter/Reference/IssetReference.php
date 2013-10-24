<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\SimpleListLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Isset;

class IssetReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Expr_Isset $node
     */
    public function __construct(PHPParser_Node_Expr_Isset $node)
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
        /* Reference
        return 'isset(' . $this->pCommaSeparated($node->vars) . ')';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add in the empty statement
        $line->add('isset(');
        // add in the actual variable reference
        $this->processArgumentList($this->node->vars, $treeNode, $line, new SimpleListLineBreak());
        // add in the closer
        $line->add(')');
    }
}
