<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Statement\OperatorAbstract;
use Magento\Tools\Formatter\PrettyPrinter\Statement\StatementAbstract;
use Magento\Tools\Formatter\Tree\NodeVisitorAbstract;
use Magento\Tools\Formatter\Tree\TreeNode;

class LineResolver extends NodeVisitorAbstract
{
    /**
     * This member holds the count of the number of statements encountered during this traversal.
     * @var int
     */
    public $statementCount = 0;

    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);

        // get the data from the node
        $nodeData = $treeNode->getData();
        // if the data represents a node, try to resolve the node
        if ($nodeData instanceof StatementAbstract || $nodeData instanceof OperatorAbstract) {
            $this->statementCount++;
            // let the statement try to resolve to a line
            $nodeData->resolve($treeNode);
        }
    }
}
