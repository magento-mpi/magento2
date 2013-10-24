<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

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
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        /** @var LineData $lineData */
        $lineData = $treeNode->getData();
        $this->processLine($lineData->line, 0, $treeNode);
    }

    /**
     * This method processes the current line data for the passed in break level.
     * @param Line $line
     * @param int $level
     * @param TreeNode $treeNode
     */
    protected function processLine(Line &$line, $level, TreeNode $treeNode)
    {
        // split the lines at the current level to check for length
        $currentLines = $line->splitLine($level);
        $valid = true;
        // determine if all the lines are within tolerances
        foreach ($currentLines as $currentLine) {
            $lineText = $currentLine[Line::ATTRIBUTE_LINE];
            // determine the length of the resulting line
            $lineLength = strlen($lineText);
            if (!array_key_exists(Line::ATTRIBUTE_NO_INDENT, $currentLine)) {
                $lineLength += $this->level * strlen(self::PREFIX);
            }
            // if the result is still longer than a line, then flag this as invalid
            if (self::MAX_LINE_LENGTH < $lineLength) {
                $valid = false;
                break;
            }
        }

        // The recursive nature of this method increments $level each time this
        // method is called.  A special case relates to $level being >= 1.
        // Currently, a ConditionalLineBreak cannot handle a value for
        // $level > 1.  Setting $valid = true prevents the recursion
        // that prevents ConditionalLineBreak from having to handle a value > 1.
        // TODO: this is still just a temporary fix, but better than the prior
        // fix which prevented splitNode() or setTokens() from being called.
        if ($level >= 1) {
            $valid = true;
        }

        // if valid, then add any extra lines
        if ($valid) {
            // only need to change things if resolved line spans multiple lines
            if (count($currentLines) > 1) {
                $this->splitNode($line, $currentLines, $treeNode);
            } else {
                $line->setTokens($currentLines[0]);
            }
        } else {
            $this->processLine($line, $level + 1, $treeNode);
        }
    }

    /**
     * This method takes the current lines and splits them around the current node.
     * @param Line $line
     * @param array $currentLines
     * @param TreeNode $treeNode
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
        if (null !== $originalChildren && $lastNode !== $treeNode) {
            foreach ($originalChildren as $originalChild) {
                $lastNode->addChild($originalChild);
            }
        }
    }
}
