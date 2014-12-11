<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

class NodeLeveler extends LevelNodeVisitor
{
    const MAX_LINE_LENGTH = 120;

    /**
     * This method is called when first visiting a node.
     *
     * @param TreeNode $treeNode Current node in the tree.
     * @return void
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        /** @var LineData $lineData */
        $lineData = $treeNode->getData();
        // check to see if the line is too long with basic line splitting
        if ($this->checkLine($lineData->line, $treeNode)) {
            /** @var Tree $subTree */
            $subTree = $this->getLineTree($lineData->line);
            if (null != $subTree) {
                $roots = $subTree->getChildren();
                if (is_array($roots)) {
                    $originalChildren = $treeNode->getChildren();
                    /** @var TreeNode $lastNode */
                    $lastNode = null;
                    foreach ($roots as $root) {
                        // replace the line on the first node
                        if (null === $lastNode) {
                            // copy the content from the root over to the current node
                            $this->copyContents($root, $treeNode);
                            // the top node is the last node
                            $lastNode = $treeNode;
                        } else {
                            // otherwise, add the next root as a sibling of the last node
                            $lastNode = $lastNode->addSibling($root);
                        }
                    }
                    // copy the children of the original node to the new last node
                    if ($lastNode !== $treeNode) {
                        // copy original children
                        $this->copyChildren($originalChildren, $lastNode);
                    }
                } else {
                    // copy the content from the root over to the current node
                    $this->copyContents($roots, $treeNode);
                }
            }
        }
    }

    /**
     * This method checks to see if the line is valid as is, or if more processing needs to be
     * done. If the node looks valid, the strings are replaced with the resolved versions of the
     * line.
     *
     * @param Line $line Line to check.
     * @param TreeNode $treeNode Current node in the tree.
     * @return bool Returns true if more processing needs to be done.
     */
    protected function checkLine(Line $line, TreeNode $treeNode)
    {
        $invalid = false;
        // split the lines at the 0th level to check for length
        $currentLines = $line->splitLine(0);
        switch (sizeof($currentLines)) {
            case 1:
                // if the one line fits on a single line, then replace the contents with the resolved line
                if ($this->fitsOnLine(current($currentLines))) {
                    $line->setTokens(current($currentLines)->getTokens());
                } else {
                    // otherwise, it needs more processing
                    $invalid = true;
                }
                break;
            default:
                $fits = true;
                $level = $this->level;
                /** @var Line $currentLine */
                foreach ($currentLines as $currentLine) {
                    if (!$this->fitsOnLine($currentLine)) {
                        $fits = false;
                        break;
                    }
                    if ($currentLine->getLastToken() instanceof HardIndentLineBreak) {
                        $level++;
                    }
                }
                // if it fits, just deal with the new nodes
                if ($fits) {
                    $this->splitNode($line, $currentLines, $treeNode);
                } else {
                    // otherwise, it needs more processing
                    $invalid = true;
                }
        }
        return $invalid;
    }

    /**
     * This method copies the children found in the array to the target node.
     *
     * @param TreeNode[] $children Array of children or single child to copy.
     * @param TreeNode $target Node to copy to.
     * @return void
     */
    public static function copyChildren($children, TreeNode $target)
    {
        if (null !== $children) {
            if (is_array($children)) {
                foreach ($children as $child) {
                    $target->addChild($child);
                }
            } else {
                $target->addChild($children);
            }
        }
    }

    /**
     * This method copies children from the source node to the target node.
     *
     * @param TreeNode $source Node containing the children to copy
     * @param TreeNode $target Node to copy to.
     * @return void
     */
    protected function copyChildrenFromNode(TreeNode $source, TreeNode $target)
    {
        if ($source->hasChildren()) {
            self::copyChildren($source->getChildren(), $target);
        }
    }

    /**
     * This method copies the line and the children from the source to the target.
     *
     * @param TreeNode $source Node to copy from.
     * @param TreeNode $target Node to copy to.
     * @return void
     */
    protected function copyContents(TreeNode $source, TreeNode $target)
    {
        // if the source line still has conditional line breaks, then resolve those
        if (sizeof($source->getData()->line->getLineBreakTokens()) > 0) {
            // split the line at level 0 as a final resort
            $currentLines = $source->getData()->line->splitLine(0);
            // use the first line returned; almost guaranteed that there will be one
            $target->getData()->line = $currentLines[0];
            // mention something if there are more than 1
            if (sizeof($currentLines) > 1) {
                echo "Last resort line split produced more than 1 line";
                echo $source->getData()->line;
            }
        } else {
            // replace the line contents
            $target->getData()->line = $source->getData()->line;
        }
        // move children from one node to the other
        $this->copyChildrenFromNode($source, $target);
    }

    /**
     * This method determines if the line fits on the current level. To fit, it must be narrow
     * enough to fit after the indents.
     *
     * @param Line $line Line representation as returned from line resolver.
     * @return bool
     */
    protected function fitsOnLine(Line $line)
    {
        // determine the length of the resulting line
        $lineText = $line->getLine();
        $lineLength = strlen($lineText);
        if (!$line->isNoIndent()) {
            $lineLength += $this->level * strlen(NodePrinter::PREFIX);
        }
        // check to see if it fits
        return self::MAX_LINE_LENGTH >= $lineLength;
    }

    /**
     * This method splits the line based on line breaks found in the line.
     *
     * @param Line $line Line to check.
     * @return Tree Best looking sub-tree representing the given line.
     */
    protected function getLineTree(Line $line)
    {
        // create a tree with the line in the root
        $subTree = new Tree();
        $subTree->addRoot(AbstractSyntax::getNodeLine($line));
        // determine which sort order produces the best results
        $sortOrders = $line->getSortedLineBreaks();
        if (sizeof($sortOrders) > 0) {
            // already checked the 0 level, so shift it out if there
            if ($sortOrders[0] === 0) {
                array_shift($sortOrders);
            }
            foreach ($sortOrders as $sortOrder) {
                // traverse the new tree, resolving by passed in sort order
                $visitor = new NodeLevelerSortOrder($sortOrder, $this->level);
                $subTree->traverse($visitor);
                // determine if all of these sub lines will fit
                $lineSizeCheck = new LineSizeCheck($this->level - 1);
                $subTree->traverse($lineSizeCheck);
                if ($lineSizeCheck->fits) {
                    break;
                }
            }
        }
        // return the best tree
        return $subTree;
    }

    /**
     * This method takes the current lines and splits them around the current node.
     *
     * @param Line &$line Line to check.
     * @param array $currentLines Line representation as returned from line resolver.
     * @param TreeNode $treeNode Current node in the tree.
     * @return void
     */
    protected function splitNode(Line &$line, array $currentLines, TreeNode $treeNode)
    {
        // save off any child nodes of the current node
        $originalChildren = $treeNode->getChildren();
        // split the lines based on resolved lines
        $lastLineBreak = null;
        $lastNode = $treeNode;
        /** @var Line $currentLine */
        foreach ($currentLines as $index => $currentLine) {
            $lineBreak = $currentLine->getLastToken();
            // replace the existing data if on the first index
            if ($index == 0) {
                $line->setTokens($currentLine->getTokens());
            } else {
                $newNode = AbstractSyntax::getNodeLine($currentLine);
                // determine the indentation based on the type of terminator on the previous line
                if ($lastLineBreak->isNextLineIndented()) {
                    $treeNode->addChild($newNode);
                } else {
                    $lastNode = $lastNode->addSibling($newNode);
                }
            }
            // save off the current line break
            $lastLineBreak = $lineBreak;
        }
        // copy the original children if there is a new last node based on the line split
        if ($lastNode !== $treeNode) {
            $this->copyChildren($originalChildren, $lastNode);
        }
    }
}
