<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\TreeNode;

class NodeLevelerSortOrder extends LineSizeCheck
{
    const INDENT_LEVEL = 'indent_level';

    const LINE_NUMBER = 'line';

    /**
     * This member hold line break data used when splitting lines.
     *
     * @var array
     */
    protected $lineBreakData;

    /**
     * This member holds the original level.
     *
     * @var int
     */
    protected $originalLevel;

    /**
     * This member holds the sort order to be used for the leveling.
     *
     * @var int
     */
    protected $sortOrder;

    /**
     * This method constructs a new level based on the given sort order.
     *
     * @param int $sortOrder
     * @param int $level Starting level for the traversal.
     */
    public function __construct($sortOrder, $level = 0)
    {
        // need to subtract one since the node entry will naturally increment the level
        parent::__construct($level - 1);
        $this->originalLevel = $level;
        $this->sortOrder = $sortOrder;
        $this->lineBreakData[self::LINE_NUMBER] = 0;
    }

    /**
     * This method is called when first visiting a node.
     *
     * @param TreeNode $treeNode Current node in the tree.
     * @return void
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        // up the line number
        $this->lineBreakData[self::LINE_NUMBER]++;
        $this->lineBreakData[self::INDENT_LEVEL] = $this->level - $this->originalLevel;
        /** @var Line $lineData */
        $line = $treeNode->getData()->line;
        if (!$this->fitsOnLine($line)) {
            $this->getLineTreeForSortOrder($line, $treeNode);
        }
    }

    /**
     * This method returns the first child of the passed in node.
     *
     * @param TreeNode $treeNode Node to find the first child.
     * @return TreeNode|null First child of the passed in node or null if no children.
     */
    protected function getFirstChild(TreeNode $treeNode)
    {
        $firstChild = null;
        // if the node has children, just grab the first one
        if ($treeNode->hasChildren()) {
            $children = $treeNode->getChildren();
            $firstChild = $children[0];
        }
        return $firstChild;
    }

    /**
     * This method splits the passed in line based on sort order of the line breaks and adds the
     * results to the passed in node.
     *
     * @param Line $line Line to check.
     * @param TreeNode $treeNode Node to append the lines to.
     * @return void
     */
    protected function getLineTreeForSortOrder(Line $line, TreeNode $treeNode)
    {
        // split the line by sort order
        $currentLines = $line->splitLineBySortOrder($this->sortOrder, $this->lineBreakData);
        // if more than a single line returned, break up the node
        if (sizeof($currentLines) > 1) {
            // determine where the lines go
            $lastTerminator = null;
            $originalFirstChild = $this->getFirstChild($treeNode);
            $originalChildren = $treeNode->getChildren();
            /** @var Line $currentLine */
            foreach ($currentLines as $currentLine) {
                // if this is the first pass, replace the current line
                if (null === $lastTerminator) {
                    $treeNode->getData()->line = $currentLine;
                } elseif ($lastTerminator instanceof HardIndentLineBreak) {
                    $treeNode->addChild(AbstractSyntax::getNodeLine($currentLine), $originalFirstChild, false);
                } else {
                    $treeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine($currentLine));
                    // if there were children prior to the split, then move those to the new sibling
                    if (!empty($originalChildren)) {
                        NodeLeveler::copyChildren($originalChildren, $treeNode);
                    }
                }
                $lastTerminator = $currentLine->getLastToken();
            }
        }
    }
}
