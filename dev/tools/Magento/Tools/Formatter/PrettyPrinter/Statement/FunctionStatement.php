<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add the function line
        $this->addToLine($treeNode, 'function ');
        // add in the reference marker
        if ($this->node->byRef) {
            $this->addToLine($treeNode, '&');
        }
        // add in the name and parameters
        $this->addToLine($treeNode, $this->node->name);
        $lineBreak = new ParameterLineBreak();
        $treeNode = $this->processArgsList($this->node->params, $treeNode, $lineBreak);
        $this->addToLine($treeNode, $lineBreak)->add('{')->add(new HardLineBreak());
        // process content of the methods
        $this->processNodes($this->node->stmts, $treeNode);
        // add closing block
        return $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
    }
}
