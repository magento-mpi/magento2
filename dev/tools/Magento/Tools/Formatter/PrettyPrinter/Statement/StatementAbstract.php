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
abstract class StatementAbstract extends AbstractSyntax
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

    /**
     * This method processes the newly added node.
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     * @param mixed $data Data that is passed to derived class when processing the node.
     * @return \Magento\Tools\Formatter\Tree\TreeNode
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total, $data = null)
    {
        // default is to add the new node as a child of the originating node
        $originatingNode->addChild($newNode);
        // always return the originating node
        return $originatingNode;
    }

    /**
     * This method parses the given nodes and places them in the tree by calling processNode. This
     * allows the derived class a chance to insert the new node into the appropriate location.
     * @param mixed $nodes Array or single node
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param mixed $data Data that is passed to derived class when processing the node.
     * @return TreeNode
     */
    protected function processNodes($nodes, TreeNode $originatingNode, $data = null)
    {
        if (is_array($nodes)) {
            $total = count($nodes);
            foreach ($nodes as $index => $node) {
                $treeNode = AbstractSyntax::getNode(SyntaxFactory::getInstance()->getStatement($node));
                $originatingNode = $this->processNode($originatingNode, $treeNode, $index, $total, $data);
            }
        } else {
            $treeNode = AbstractSyntax::getNode(SyntaxFactory::getInstance()->getStatement($nodes));
            $originatingNode = $this->processNode($originatingNode, $treeNode, 0, 1, $data);
        }
        // return the last node that was added (or whatever was returned from the last node processing)
        return $originatingNode;
    }
}
