<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ReferenceAbstract;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Arg;

class ArgumentReference extends ReferenceAbstract
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Arg $node
     */
    public function __construct(PHPParser_Node_Arg $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        /* Reference
        return ($node->byRef ? '&' : '') . $this->p($node->value);
        */
        /** @var Line $line */
        $line = $treeNode->getData();
        // add the reference, if needed
        if ($this->node->byRef) {
            $line->add('&');
        }
        // add in the actual variable reference
        $this->resolveNode($this->node->value, $treeNode);
    }
}