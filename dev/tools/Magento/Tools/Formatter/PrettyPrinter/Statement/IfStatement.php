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
use Magento\Tools\Formatter\PrettyPrinter\WrapperLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_If;

class IfStatement extends AbstractConditionalStatement
{
    /**
     * This method constructs a new statement based on the specified if statement.
     * @param PHPParser_Node_Stmt_If $node
     */
    public function __construct(PHPParser_Node_Stmt_If $node)
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
        return 'if (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}'
             . $this->pImplode($node->elseifs)
             . (null !== $node->else ? $this->p($node->else) : '');
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the if line
        $lineBreak = new WrapperLineBreak();
        $line->add('if (')->add($lineBreak);
        // add in the condition
        $this->resolveNode($this->node->cond, $treeNode);
        $line->add($lineBreak)->add(') {')->add(new HardLineBreak());
        // processing the child nodes
        $this->processNodes($this->node->stmts, $treeNode, true);
        // process elseif statements
        if (!empty($this->node->elseifs)) {
            $treeNode = $this->processNodes($this->node->elseifs, $treeNode, false);
        }
        // process else statements
        if (null !== $this->node->else) {
            $treeNode = $this->processNodes($this->node->else, $treeNode, false);
        }
        // add the closing brace on a new line
        $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
    }

    /**
     * This method processes the newly added node.
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     * @param mixed $data Data that is passed to derived class when processing the node.
     * @return TreeNode Returns the originating node since just children are being added.
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total, $data = null)
    {
        if ($data) {
            // adding the statements inside the conditional, so add them as children
            $originatingNode->addChild($newNode);
        } else {
            // this is called to add the elseif and else nodes; therefore, add it as a sibling to the originating node
            $originatingNode = $originatingNode->addSibling($newNode);
        }
        return $originatingNode;
    }
}
