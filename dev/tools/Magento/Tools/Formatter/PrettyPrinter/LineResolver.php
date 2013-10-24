<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Statement\ReferenceAbstract;
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
        /** @var LineData $lineData */
        $lineData = $treeNode->getData();
        // if the syntax has not been resolved, then try to resolve it
        if (null === $lineData->line) {
            $this->statementCount++;
            // let the syntax try to resolve to a line
            $lineData->syntax->resolve($treeNode);
            // if there is only a reference, then add a line terminator
            if ($lineData->syntax instanceof ReferenceAbstract) {
                $lineData->line->add(';')->add(new HardLineBreak());
            }
        }
    }
}
