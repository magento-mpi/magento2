<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Case;

class CaseStatement extends AbstractConditionalStatement
{
    /**
     * This method constructs a new statement based on the specified case statement.
     *
     * @param PHPParser_Node_Stmt_Case $node
     */
    public function __construct(PHPParser_Node_Stmt_Case $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add the control word
        if (null !== $this->node->cond) {
            $this->addToLine($treeNode, 'case ');
            $treeNode = $this->resolveNode($this->node->cond, $treeNode);
        } else {
            $this->addToLine($treeNode, 'default');
        }
        $this->addToLine($treeNode, ':')->add(new HardLineBreak());
        // add in the statements
        return $this->processNodes($this->node->stmts, $treeNode);
    }

    /**
     * This method adds the current comment line to the current tree node.
     *
     * @param string $commentLine String containing the current comment.
     * @param TreeNode $treeNode TreeNode representing the current node.
     * @return void
     */
    protected function addCommentToNode($commentLine, TreeNode $treeNode)
    {
        // cases should not have their own comments; if they do, add it as the last child to the sibling node
        $siblingNode = $this->getPriorSibling($treeNode->getParent()->getChildren(), $treeNode);
        // if found, then add the comment
        if (isset($siblingNode)) {
            $newNode = AbstractSyntax::getNodeLine((new Line($commentLine))->add(new HardLineBreak()));
            $siblingNode->addChild($newNode);
        } else {
            // otherwise, just let the base class do common action
            parent::addCommentToNode($commentLine, $treeNode);
        }
    }

    /**
     * This method returns the previous node to the passed in node. Null is returned if not found
     * or found as the first child.
     *
     * @param array $children Array of children to search
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode|null
     */
    protected function getPriorSibling(array $children, TreeNode $treeNode)
    {
        // assume not found
        $siblingNode = null;
        // make sure the array pointer is starting at the beginning
        reset($children);
        // look for the exact match
        $found = false;
        while (!$found && next($children)) {
            $found = current($children) === $treeNode;
        }
        // if found, then really want the previous node
        if ($found) {
            $siblingNode = prev($children);
        }
        return $siblingNode;
    }
}
