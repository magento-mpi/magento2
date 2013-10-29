<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_ArrayItem;

class ArrayItemReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Expr_ArrayItem $node
     */
    public function __construct(PHPParser_Node_Expr_ArrayItem $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the array item to the end of the current line
        if (null !== $this->node->key) {
            $this->resolveNode($this->node->key, $treeNode);
            $line->add(' => ');
        }
        if ($this->node->byRef) {
            $line->add('&');
        }
        $this->resolveNode($this->node->value, $treeNode);
    }
}
