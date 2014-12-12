<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Class;

/**
 * This class is the base class for all printer statements.
 */
abstract class AbstractStatement extends AbstractSyntax
{
    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into
     * lines. Derived classes must replace the statement in the tree, or this method will repeat
     * comments.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        return parent::resolve($treeNode);
    }

    /**
     * This method adds modifiers to the line based on the bit map passed in.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     * @param int $modifiers Bit map containing the markers for the various modifiers.
     * @return void
     */
    protected function addModifier(TreeNode $treeNode, $modifiers)
    {
        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) {
            $this->addToLine($treeNode, 'abstract ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) {
            $this->addToLine($treeNode, 'final ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) {
            $this->addToLine($treeNode, 'public ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) {
            $this->addToLine($treeNode, 'protected ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) {
            $this->addToLine($treeNode, 'private ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) {
            $this->addToLine($treeNode, 'static ');
        }
    }
}
