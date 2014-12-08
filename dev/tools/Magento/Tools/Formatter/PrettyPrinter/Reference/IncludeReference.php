<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Include;

class IncludeReference extends AbstractReference
{
    /**
     * @var array
     */
    public static $map = [
        PHPParser_Node_Expr_Include::TYPE_INCLUDE => 'include',
        PHPParser_Node_Expr_Include::TYPE_INCLUDE_ONCE => 'include_once',
        PHPParser_Node_Expr_Include::TYPE_REQUIRE => 'require',
        PHPParser_Node_Expr_Include::TYPE_REQUIRE_ONCE => 'require_once',
    ];

    /**
     * This method constructs a new statement based on the specified use clause.
     *
     * @param PHPParser_Node_Expr_Include $node
     */
    public function __construct(PHPParser_Node_Expr_Include $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // Add token with space
        $this->addToLine($treeNode, self::$map[$this->node->type])->add(' ');
        // Resolve expr
        return $this->resolveNode($this->node->expr, $treeNode);
    }
}
