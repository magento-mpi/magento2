<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Closure;

class ClosureReference extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Expr_Closure $node
     */
    public function __construct(PHPParser_Node_Expr_Closure $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current reference, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add in static, if specified
        if ($this->node->static) {
            $line->add('static ');
        }
        // add in the function word
        $line->add('function ');
        // add in the reference specifier
        if ($this->node->byRef) {
            $line->add('&');
        }
        // add in the parameters
        $line->add('(');
        $this->processArgumentList($this->node->params, $treeNode, $line, new CallLineBreak());
        $line->add(')');
        // add in uses, if specified
        if (!empty($this->node->uses)) {
            $line->add(' use (');
            $this->processArgumentList($this->node->uses, $treeNode, $line, new CallLineBreak());
            $line->add(')');
        }
        // add in enclosures and children
        $line->add(' {')->add(new HardLineBreak());
        $this->processNodes($this->node->stmts, $treeNode);
        $treeNode->addSibling(AbstractSyntax::getNodeLine(new Line('}')));
    }
}
