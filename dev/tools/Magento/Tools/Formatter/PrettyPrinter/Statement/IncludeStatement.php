<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Include;

class IncludeStatement extends AbstractStatement
{
    /*
    public function pExpr_Include(PHPParser_Node_Expr_Include $node) {
        static $map = array(
            PHPParser_Node_Expr_Include::TYPE_INCLUDE      => 'include',
            PHPParser_Node_Expr_Include::TYPE_INCLUDE_ONCE => 'include_once',
            PHPParser_Node_Expr_Include::TYPE_REQUIRE      => 'require',
            PHPParser_Node_Expr_Include::TYPE_REQUIRE_ONCE => 'require_once',
        );

        return $map[$node->type] . ' ' . $this->p($node->expr);
    }
    */
    public static $map = array(
        PHPParser_Node_Expr_Include::TYPE_INCLUDE      => 'include',
        PHPParser_Node_Expr_Include::TYPE_INCLUDE_ONCE => 'include_once',
        PHPParser_Node_Expr_Include::TYPE_REQUIRE      => 'require',
        PHPParser_Node_Expr_Include::TYPE_REQUIRE_ONCE => 'require_once',
    );

    /**
     * This method constructs a new statement based on the specified use clause.
     * @param PHPParser_Node_Expr_Include $node
     */
    public function __construct(PHPParser_Node_Expr_Include $node)
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
        // Add token with space
        $line->add(self::$map[$this->node->type])->add(' ');
        // Resolve expr
        $this->resolveNode($this->node->expr, $treeNode);
        // Add line termination
        $line->add(';')->add(new HardLineBreak());
    }
}
