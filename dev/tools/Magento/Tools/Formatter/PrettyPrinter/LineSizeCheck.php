<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\TreeNode;

class LineSizeCheck extends LevelNodeVisitor
{
    /**
     * This member holds the result of the traversal.
     *
     * @var bool
     */
    public $fits = true;

    /**
     * This method constructs a new visitor with the given starting level.
     *
     * @param int $level Starting level for the traversal.
     */
    public function __construct($level = -1)
    {
        parent::__construct($level);
    }

    /**
     * This method is called when first visiting a node.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        // flag this line as a failure
        if (!$this->fitsOnLine($treeNode->getData()->line)) {
            $this->fits = false;
        }
    }

    /**
     * This method determines if the line fits on the current level. To fit, it must resolve to a
     * single line and must be narrow enough to fit after the indents.
     *
     * @param Line $line Instance to resolve.
     * @return bool
     */
    protected function fitsOnLine(Line $line)
    {
        $fits = false;
        // split the lines at the 0th level to check for length
        $currentLines = $line->splitLine(0);
        if (1 === sizeof($currentLines)) {
            $lineText = current($currentLines)->getLine();
            // determine the length of the resulting line
            $lineLength = strlen($lineText);
            if (!current($currentLines)->isNoIndent()) {
                $lineLength += $this->level * strlen(NodePrinter::PREFIX);
            }
            // check to see if it fits
            if (NodeLeveler::MAX_LINE_LENGTH >= $lineLength) {
                // replace the line tokens with the resolved tokens
                $line->setTokens(current($currentLines)->getTokens());
                // flag that this line is valid
                $fits = true;
            }
        }
        return $fits;
    }
}
