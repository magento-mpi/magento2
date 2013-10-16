<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

class NodeLeveler extends LevelNodeVisitor
{
    const MAX_LINE_LENGTH = 80;

    /**
     * This member holds what is being used as a prefix to the line (i.e. 4 spaces).
     */
    const PREFIX = '    ';

    /**
     * This member holds the tree for which the nodes are going to be traversed.
     * @var Tree
     */
    protected $tree;

    /**
     * This member constructs a new node leveler instance for the given tree.
     * @param Tree $tree The tree which is being traversed.
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        // get the data from the node
        $lineData = $treeNode->getData();
        $this->processLine($lineData, 0, $treeNode);
    }

    /**
     * This method processes the current line data for the passed in break level.
     * @param $lineData
     */
    protected function processLine($lineData, $level, $treeNode)
    {
        if (null !== $lineData) {
            // split the line info in case is spans multiple lines
            $lines = $lineData->splitLines($level);
            // if the line represents more than one line, then split it up
            if (sizeof($lines) > 1) {
                $lastLineBreak = null;
                foreach ($lines as $index => $line) {
                    $lineBreak = $line[sizeof($line) - 1];
                    if ($index == 0) {
                        $lineData->setTokens($line);
                    } else {
                        // reset the current node to avoid the additions changing it
                        $this->tree->setCurrentNode($treeNode);
                        // determine the indentation based on the type of terminator on the previous line
                        if ($lastLineBreak->isNextLineIndented()) {
                            $this->tree->addChild(new TreeNode(new Line($line)), false);
                        } else {
                            $this->tree->addSibling(new TreeNode(new Line($line)));
                        }
                    }
                    $lineBreak->setAlternate(false);
                    $lastLineBreak = $lineBreak;
                }
            } else {
                $line = $lines[0];
                $result = implode('', $line);
                if (self::MAX_LINE_LENGTH < strlen($result) + $this->level * strlen(self::PREFIX)) {
                    // split the line info in case is spans multiple lines
                    $this->processLine($lineData, $level + 1, $treeNode); // TODO protect against infinite loop
                }
            }
        }
    }
}
