<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Catch;

class CatchStatement extends AbstractControlStatement
{
    /**
     * This method constructs a new statement based on the specified catch node.
     * @param PHPParser_Node_Stmt_Catch $node
     */
    public function __construct(PHPParser_Node_Stmt_Catch $node)
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
        // add the try line
        $this->addToLine($treeNode, '} catch (');
        // add in the type of exception
        $treeNode = $this->resolveNode($this->node->type, $treeNode);
        // add in the variable of the exception
        $this->addToLine($treeNode, ' $')->add($this->node->var)->add(') {')->add(new HardLineBreak());
        // add in the statements inside the catch
        return $this->processNodes($this->node->stmts, $treeNode);
    }
}
