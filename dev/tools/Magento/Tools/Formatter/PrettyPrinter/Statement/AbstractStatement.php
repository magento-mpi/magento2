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
use Magento\Tools\Formatter\PrettyPrinter\LineData;
use Magento\Tools\Formatter\PrettyPrinter\SyntaxFactory;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Class;

/**
 * This class is the base class for all printer statements.
 */
abstract class AbstractStatement extends AbstractSyntax
{
    const ATTRIBUTE_COMMENTS = 'comments';
    protected $trimComments = false;

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into
     * lines. Derived classes must replace the statement in the tree, or this method will repeat
     * comments.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // all statements have potential of having comments, so resolve those
        $this->addCommentsBefore($treeNode);
    }

    /**
     * This method adds any comments in the current node as prior siblings to the current node.
     * @param TreeNode $treeNode Node representing the current node.
     */
    protected function addCommentsBefore(TreeNode $treeNode)
    {
        // only attempt to add comments if they are present
        if ($this->node->hasAttribute(self::ATTRIBUTE_COMMENTS)) {
            // add individual lines of the comments to the tree
            $comments = $this->node->getAttribute(self::ATTRIBUTE_COMMENTS);
            $this->trimComments($comments);
            foreach ($comments as $comment) {
                // split the lines so that they can be indented correctly
                $commentLines = explode(HardLineBreak::EOL, $comment->getReformattedText());
                foreach ($commentLines as $commentLine) {
                    // add the line individually to the tree so that they can be indented correctly
                    $newNode = AbstractSyntax::getNodeLine((new Line($commentLine))->add(new HardLineBreak()));
                    $treeNode->addSibling($newNode, false);
                }
            }
        }
    }

    protected function trimComments(&$comments)
    {
        $numComments = sizeof($comments);
        if ($this->trimComments && $numComments > 0) {
            if ($numComments > 1) {
                if (preg_match('/^\s*\n$/', $comments[0])) {
                    // Remove it
                    array_shift($comments);
                    // Reduce the number of comments
                    $numComments--;
                }
            }
            if ($numComments != 0 && preg_match('/^\s*\n$/', $comments[$numComments-1])) {
                // Remove it
                array_pop($comments);
            }

        }
    }

    /**
     * This method adds modifiers to the line based on the bit map passed in.
     * @param mixed $modifiers Bit map containing the markers for the various modifiers.
     * @param Line $line Instance of line to add modifier.
     */
    protected function addModifier($modifiers, Line $line)
    {
        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) {
            $line->add('abstract ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_FINAL) {
            $line->add('final ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) {
            $line->add('public ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) {
            $line->add('protected ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) {
            $line->add('private ');
        }

        if ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) {
            $line->add('static ');
        }
    }
}
