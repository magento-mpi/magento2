<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\TreeNode;

class NodeLevelerSortOrder extends LineSizeCheck
{
    /**
     * This member holds the sort order to be used for the leveling.
     * @var int
     */
    protected $sortOrder;

    /**
     * This method constructs a new level based on the given sort order.
     * @param int $sortOrder
     * @param int $level Starting level for the traversal.
     */
    public function __construct($sortOrder, $level = -1)
    {
        parent::__construct($level);
        $this->sortOrder = $sortOrder;
    }
    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode Current node in the tree.
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        /** @var Line $lineData */
        $line = $treeNode->getData()->line;
        if (!$this->fitsOnLine($line)) {
            $this->getLineTreeForSortOrder($line, $treeNode);
        }
    }

    /**
     * This method splits the passed in line based on sort order of the line breaks and adds the
     * results to the passed in node.
     * @param Line $line Line to check.
     * @param TreeNode $treeNode Node to append the lines to.
     */
    protected function getLineTreeForSortOrder(Line $line, TreeNode $treeNode)
    {
        // split the line by sort order
        $currentLines = $line->splitLineBySortOrder($this->sortOrder);
        // if more than a single line returned, break up the node
        if (sizeof($currentLines) > 1) {
            // determine where the lines go
            $lastTerminator = null;
            /** @var Line $currentLine */
            foreach ($currentLines as $currentLine) {
                // if this is the first pass, replace the current line
                if (null === $lastTerminator) {
                    $treeNode->getData()->line = $currentLine;
                } elseif ($lastTerminator instanceof HardIndentLineBreak) {
                    $treeNode->addChild(AbstractSyntax::getNodeLine($currentLine));
                } else {
                    $treeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine($currentLine));
                }
                $lastTerminator = $currentLine->getLastToken();
            }
        }
    }
}
