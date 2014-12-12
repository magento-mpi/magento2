<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\ParameterLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_ClassMethod;

class MethodStatement extends ClassMemberAbstract
{
    /**
     * This method constructs a new statement based on the specified class method.
     * @param PHPParser_Node_Stmt_ClassMethod $node
     */
    public function __construct(PHPParser_Node_Stmt_ClassMethod $node)
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
        // add the class line
        $this->addModifier($treeNode, $this->node->type);
        $this->addToLine($treeNode, 'function ');
        if ($this->node->byRef) {
            $this->addToLine($treeNode, '&');
        }
        // note if method has statements
        $hasStatements = null !== $this->node->stmts;
        // add in the parameters
        $lineBreak = $hasStatements ? new ParameterLineBreak() : new CallLineBreak();
        $this->addToLine($treeNode, $this->node->name);
        $this->processArgsList($this->node->params, $treeNode, $lineBreak);
        // add in the optional statements
        if ($hasStatements) {
            $this->addToLine($treeNode, $lineBreak)->add('{')->add(new HardLineBreak());
            // process content of the methods
            $this->processNodes($this->node->stmts, $treeNode);
            // add closing block
            $treeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
        } else {
            // no statements, so assume it is an abstract class and terminate the line
            $this->addToLine($treeNode, ';')->add(new HardLineBreak());
        }
        return $treeNode;
    }
}
