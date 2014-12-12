<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardIndentLineBreak;
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
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add in static, if specified
        if ($this->node->static) {
            $this->addToLine($treeNode, 'static ');
        }
        // add in the function word
        $this->addToLine($treeNode, 'function ');
        // add in the reference specifier
        if ($this->node->byRef) {
            $this->addToLine($treeNode, '&');
        }
        // add in the parameters
        $treeNode = $this->processArgsList($this->node->params, $treeNode, new CallLineBreak());
        // add in uses, if specified
        if (!empty($this->node->uses)) {
            $this->addToLine($treeNode, ' use ');
            $this->processArgsList($this->node->uses, $treeNode, new CallLineBreak());
        }
        // add in enclosures and children
        $this->addToLine($treeNode, ' {')->add(new HardIndentLineBreak());
        $this->processNodes($this->node->stmts, $treeNode);
        return $treeNode->addSibling(AbstractSyntax::getNodeLine(new Line('}')));
    }
}
