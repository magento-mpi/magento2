<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Statement\AbstractStatement;
use Magento\Tools\Formatter\Tree\NodeVisitorAbstract;
use Magento\Tools\Formatter\Tree\TreeNode;

class LineResolver extends NodeVisitorAbstract
{
    /**
     * This member holds the count of the number of statements encountered during this traversal.
     *
     * @var int
     */
    public $statementCount = 0;

    /**
     * This method is called when first visiting a node.
     *
     * @param TreeNode $treeNode
     * @return void
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
            $newNode = $lineData->syntax->resolve($treeNode);
            // if there is only a reference, then add a line terminator
            if (!$lineData->syntax instanceof AbstractStatement) {
                $line = $lineData->line;
                if (null !== $newNode && $treeNode !== $newNode) {
                    $line = $newNode->getData()->line;
                }
                $line->add(';')->add(new HardLineBreak());
            }
        }
    }

    /**
     * This method adds any comments in the current node as prior siblings to the current node.
     *
     * @param LineData $lineData
     * @param TreeNode $treeNode Node representing the current node.
     * @return void
     */
    protected function addCommentsBefore(LineData $lineData, TreeNode $treeNode)
    {
        // Only the syntax object has comments attached to it. So if there isn't one there are probably no comments
        if ($lineData->syntax !== null) {
            $lineData->syntax->addComments($treeNode);
        }
    }
}
