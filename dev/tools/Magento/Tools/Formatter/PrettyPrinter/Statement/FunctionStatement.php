<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\ParameterLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Function;

class FunctionStatement extends AbstractScriptStatement
{
    /**
     * This method constructs a new statement based on the specified function
     * @param PHPParser_Node_Stmt_Function $node
     */
    public function __construct(PHPParser_Node_Stmt_Function $node)
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
        // add the function line
        $line->add('function ');
        // add in the reference marker
        if ($this->node->byRef) {
            $line->add('&');
        }
        // add in the name and parameters
        $line->add($this->node->name)->add('(');
        $lineBreak = new ParameterLineBreak();
        $this->processArgumentList($this->node->params, $treeNode, $line, $lineBreak);
        $line->add(')')->add($lineBreak)->add('{')->add(new HardLineBreak());
        // process content of the methods
        $this->processNodes($this->node->stmts, $treeNode);
        // add closing block
        $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
    }
}
