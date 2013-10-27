<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\TreeNode;

class NodePrinter extends LevelNodeVisitor
{
    /**
     * This member holds what is being used as a prefix to the line (i.e. 4 spaces).
     */
    const PREFIX = '    ';

    /**
     * This member holds the result of the traversal.
     * @var string
     */
    public $result = '';

    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        // add the line data base on indents
        $line = $treeNode->getData()->line->getLine();
        // only prepend the prefix if the line is more than a LF
        if (strlen($line) > 1 && !$treeNode->getData()->line->isNoIndent()) {
            $line = str_repeat(self::PREFIX, $this->level) . $line;
        }
        // dump an error to the console if the line is long
        if (NodeLeveler::MAX_LINE_LENGTH < strlen($line)) {
            echo "Warning: Line Longer Than Max (" . strlen($line) . " > " . NodeLeveler::MAX_LINE_LENGTH . ')';
            echo "\n-----\n$line\n-----\n";
        }
        // add the resulting string
        $this->result .= $line;
    }
}
