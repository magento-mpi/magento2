<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Catch;

class CatchStatement extends AbstractControlStatement
{
    /**
     * This method constructs a new statement based on the specify catch node.
     * @param PHPParser_Node_Stmt_Catch $node
     */
    public function __construct(PHPParser_Node_Stmt_Catch $node)
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
        return ' catch (' . $this->p($node->type) . ' $' . $node->var . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the try line
        $line->add('} catch (');
        // add in the type of exception
        $this->resolveNode($this->node->type, $treeNode);
        // add in the variable of the exception
        $line->add(' $')->add($this->node->var)->add(') {')->add(new HardLineBreak());
        // add in the statements inside the catch
        $this->processNodes($this->node->stmts, $treeNode);
    }
}
