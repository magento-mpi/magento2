<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\ConditionalLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_For;

class ForStatement extends AbstractLoopStatement
{
    /**
     * This method constructs a new statement based on the specified for statement.
     * @param PHPParser_Node_Stmt_For $node
     */
    public function __construct(PHPParser_Node_Stmt_For $node)
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
        // add the namespace line
        $this->addToLine($treeNode, 'for (');
        // add in the init expression
        $lineBreak = new ConditionalLineBreak([['']]);
        $this->processArgumentList($this->node->init, $treeNode, $lineBreak);
        $this->addToLine($treeNode, ';');
        if (!empty($this->node->cond)) {
            $this->addToLine($treeNode, ' ');
            $this->processArgumentList($this->node->cond, $treeNode, $lineBreak);
        }
        $this->addToLine($treeNode, ';');
        if (!empty($this->node->loop)) {
            $this->addToLine($treeNode, ' ');
            $this->processArgumentList($this->node->loop, $treeNode, $lineBreak);
        }
        // add in the rest
        return $this->addBody($treeNode);
    }
}
