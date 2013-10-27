<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\Node;
use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

class NodeLeveler extends LevelNodeVisitor
{
    const MAX_LINE_LENGTH = 120;

    /**
     * This member holds what is being used as a prefix to the line (i.e. 4 spaces).
     */
    const PREFIX = '    ';

    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode Current node in the tree.
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        /** @var LineData $lineData */
        $lineData = $treeNode->getData();
        if ($this->checkLine($lineData->line, $treeNode)) {
            /** @var Tree $subTree */
            $subTree = $this->getLineTree($lineData->line);
            if (null != $subTree) {
                $roots = $subTree->getChildren();
                if (is_array($roots)) {
                    $originalChildren = $treeNode->getChildren();
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
     * @param Line $line Line to check.
     * @param TreeNode $treeNode Current node in the tree.
     * @return bool Returns true if more processing needs to be done.
     */
    protected function checkLine(Line $line, TreeNode $treeNode)
    {
        $requiresMoreProcessing = false;
        // split the lines at the 0th level to check for length
        $currentLines = $line->splitLine(0);
        switch (sizeof($currentLines)) {
            case 1:
                // if the one line fits on a single line, then replace the contents with the resolved line
                if ($this->fitsOnLine(current($currentLines))) {
                    $line->setTokens(current($currentLines));
                } else {
                    // otherwise, it needs more processing
                    $requiresMoreProcessing = true;
                }
                break;
            default:
                $fits = true;
                $level = $this->level;
                foreach($currentLines as $currentLine) {
                    if (!$this->fitsOnLine($currentLine)) {
                        $fits = false;
                        break;
                    }
                    if ($currentLine[Line::ATTRIBUTE_TERMINATOR] instanceof HardIndentLineBreak) {
                        $level++;
                    }
                }
                // if it fits, just deal with the new nodes
                if ($fits) {
                    $this->splitNode($line, $currentLines, $treeNode);
                } else {
                    // otherwise, it needs more processing
                    $requiresMoreProcessing = true;
                }
        }
        return $requiresMoreProcessing;
    }

    /**
     * This method copies the children found in the array to the target node.
     * @param mixed $children Array of children or single child to copy.
     * @param TreeNode $target Node to copy to.
     */
    protected function copyChildren($children, TreeNode $target) {
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
     * @param TreeNode $source Node containing the children to copy
     * @param TreeNode $target Node to copy to.
     */
    protected function copyChildrenFromNode(TreeNode $source, TreeNode $target) {
        if ($source->hasChildren()) {
            $this->copyChildren($source->getChildren(), $target);
        }
    }

    /**
     * This method copies the line and the children from the source to the target.
     * @param TreeNode $source Node to copy from.
     * @param TreeNode $target Node to copy to.
     */
    protected function copyContents(TreeNode $source, TreeNode $target) {
        // replace the line contents
        $target->getData()->line = $source->getData()->line;
        // move children from one node to the other
        $this->copyChildrenFromNode($source, $target);
    }

    /**
     * This method determines if the line fits on the current level. To fit, it must be narrow
     * enough to fit after the indents.
     * @param array $line Line representation as returned from line resolver.
     * @return bool
     */
    protected function fitsOnLine(array $line)
    {
        // determine the length of the resulting line
        $lineLength = strlen($line[Line::ATTRIBUTE_LINE]);
        if (!array_key_exists(Line::ATTRIBUTE_NO_INDENT, $line)) {
            $lineLength += $this->level * strlen(self::PREFIX);
        }
        // check to see if it fits
        return self::MAX_LINE_LENGTH >= $lineLength;
    }

    /**
     * This method splits the line based on line breaks found in the line.
     * @param Line $line Line to check.
     * @return Tree Best looking sub-tree representing the given line.
     */
    protected function getLineTree(Line $line) {
        $sortOrders = $line->getSortedLineBreaks();
        // determine which sort order produces the best results
        foreach ($sortOrders as $sortOrder) {
            $subTree = new Tree();
            $this->getLineTreeForSortOrder($line, $sortOrder, $subTree);
            // determine if all of these sub lines will fit
            $lineSizeCheck = new LineSizeCheck($this->level - 1);
            $subTree->traverse($lineSizeCheck);
            if ($lineSizeCheck->fits) {
                break;
            }
        }
        // return the best tree
        return $subTree;
    }

    /**
     * This method splits the passed in line based on sort order of the line breaks and adds the
     * results to the passed in node.
     * @param Line $line Line to check.
     * @param int $sortOrder Sort order indicator to use to split the line.
     * @param Node $treeNode Node to append the lines to.
     */
    protected function getLineTreeForSortOrder(Line $line, $sortOrder, Node $treeNode) {
        $currentLines = $line->splitLineBySortOrder($sortOrder);
        $lastTerminator = new HardLineBreak();
        foreach ($currentLines as $currentLine) {
            if ($lastTerminator instanceof HardIndentLineBreak) {
                $treeNode->addChild(AbstractSyntax::getNodeLine($currentLine));
            } else {
                $treeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine($currentLine));
            }
            $lastTerminator = $currentLine->getLastToken();
        }
    }

    /**
     * This method takes the current lines and splits them around the current node.
     * @param Line $line Line to check.
     * @param array $currentLines Line representation as returned from line resolver.
     * @param TreeNode $treeNode Current node in the tree.
     */
    protected function splitNode(Line &$line, array $currentLines, TreeNode $treeNode)
    {
        // save off any child nodes of the current node
        $originalChildren = $treeNode->getChildren();
        // split the lines based on resolved lines
        $lastLineBreak = null;
        $lastNode = $treeNode;
        foreach ($currentLines as $index => $currentLine) {
            $lineBreak = $currentLine[Line::ATTRIBUTE_TERMINATOR];
            // replace the existing data if on the first index
            if ($index == 0) {
                $line->setTokens($currentLine);
            } else {
                $newNode = AbstractSyntax::getNodeLine(new Line($currentLine));
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
