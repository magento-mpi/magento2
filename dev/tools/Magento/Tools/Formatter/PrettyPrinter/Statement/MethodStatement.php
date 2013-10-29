<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\ParameterLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_ClassMethod;

class MethodStatement extends ClassMemberAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Stmt_ClassMethod $node
     */
    public function __construct(PHPParser_Node_Stmt_ClassMethod $node)
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
        // add the class line
        $this->addModifier($this->node->type, $line);
        $line->add('function ');
        if ($this->node->byRef) {
            $line->add('&');
        }
        // note if method has statements
        $hasStatements = null !== $this->node->stmts;
        // add in the parameters
        $lineBreak = $hasStatements ? new ParameterLineBreak() : new CallLineBreak();
        $line->add($this->node->name)->add('(');
        $this->processArgumentList($this->node->params, $treeNode, $line, $lineBreak);
        $line->add(')');
        // add in the optional statements
        if ($hasStatements) {
            $line->add($lineBreak)->add('{')->add(new HardLineBreak());
            // process content of the methods
            $this->processNodes($this->node->stmts, $treeNode);
            // add closing block
            $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
        } else {
            // no statements, so assume it is an abstract class and terminate the line
            $line->add(';')->add(new HardLineBreak());
        }
    }
}
