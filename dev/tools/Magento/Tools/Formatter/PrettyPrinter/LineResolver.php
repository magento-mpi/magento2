<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Statement\AbstractStatement;
use Magento\Tools\Formatter\PrettyPrinter\Reference\AbstractReference;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ClassMemberAbstract;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ClassTypeAbstract;
use Magento\Tools\Formatter\PrettyPrinter\Statement\NamespaceStatement;
use Magento\Tools\Formatter\Tree\NodeVisitorAbstract;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Comment;

class LineResolver extends NodeVisitorAbstract
{
    /**
     * This member holds the count of the number of statements encountered during this traversal.
     * @var int
     */
    public $statementCount = 0;

    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        /** @var LineData $lineData */
        $lineData = $treeNode->getData();
        // if the syntax has not been resolved, then try to resolve it
        if (null === $lineData->line) {
            // Handle Comments
            $this->addCommentsBefore($lineData, $treeNode);
            // Increment Statement Count
            $this->statementCount++;
            // let the syntax try to resolve to a line
            $treeNode = $lineData->syntax->resolve($treeNode);
            // if there is only a reference, then add a line terminator
            if (!$lineData->syntax instanceof AbstractStatement) {
                $line = $lineData->line;
                if (null !== $treeNode) {
                    $line = $treeNode->getData()->line;
                }
                $line->add(';')->add(new HardLineBreak());
            }
        }
    }

    /**
     * This method adds any comments in the current node as prior siblings to the current node.
     * @param TreeNode $treeNode Node representing the current node.
     */
    protected function addCommentsBefore(LineData $lineData, TreeNode $treeNode)
    {
        // Only the syntax object has comments attached to it. So if there isn't one there are probably no comments
        if ($lineData->syntax !== null) {
            // Make sure we add the comments
            $comments = $this->getComments($lineData->syntax);
            // only attempt to add comments if they are present
            if ($comments !== null && is_array($comments)) {
                // add individual lines of the comments to the tree
                foreach ($comments as $comment) {
                    // Remove comment from map since it is being consumed
                    if ($comment instanceof PHPParser_Comment) {
                        unset(Printer::$lexer->commentMap[$comment->getLine()]);
                    }
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
    }

    /**
     * This helper method gets the comments out of the AbstractSyntax object if they are there and returns the array
     * or null if there is none.
     *
     * @param AbstractSyntax $syntax
     * @return mixed
     */
    protected function getComments(AbstractSyntax $syntax)
    {
        // Get the comments if any
        $comments = $syntax->getComments();
        // If the comments are returned and this type of syntax object requires trimming trim.
        if ($comments !== null && $syntax->isTrimComments()) {
            $this->trimComments($comments);
        }
        return $comments;
    }

    /**
     * This method will modify the array that is passed in to remove blank lines in the first or last position of the
     * array.
     *
     * @param $comments
     */
    protected function trimComments(&$comments)
    {
        // reset and end will short circuit the loops when the array is empty
        // Trim blank lines before the comment
        while (reset($comments) && preg_match('/^\s*\n$/', reset($comments))) {
            array_shift($comments);
        }
        // Trim blank lines at the end of the comment
        while (end($comments) && preg_match('/^\s*\n$/', end($comments))) {
            array_pop($comments);
        }
    }
}
