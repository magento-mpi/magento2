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
    const MAX_LINE_LENGTH = 80;

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
        $this->result .= str_repeat(self::PREFIX, $this->level) . $treeNode->getData()->getLine();
    }
}
