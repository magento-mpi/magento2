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
use PHPParser_Node_Expr_StaticPropertyFetch;

class StaticPropertyReference extends AbstractReference
{
    /**
     * This method constructs a new reference based on the specified constant.
     * @param PHPParser_Node_Expr_StaticPropertyFetch $node
     */
    public function __construct(PHPParser_Node_Expr_StaticPropertyFetch $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current reference, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /* Reference
                return $this->p($node->class) . '::$' . $this->pObjectProperty($node->name);
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the class reference
        $this->resolveNode($this->node->class, $treeNode);
        // add in the actual reference
        $line->add('::$');
        // if the name is an expression, then use the framework to resolve
        if ($this->node->name instanceof PHPParser_Node_Expr) {
            $this->resolveNode($this->node->name, $treeNode);
        } else {
            // otherwise, just use the name
            $line->add($this->node->name);
        }
    }
}
