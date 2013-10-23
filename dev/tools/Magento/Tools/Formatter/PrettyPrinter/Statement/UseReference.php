<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_UseUse;

class UseReference extends ReferenceAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Stmt_UseUse $node
     */
    public function __construct(PHPParser_Node_Stmt_UseUse $node)
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
        /* Reference
        return $this->p($node->name)
             . ($node->name->getLast() !== $node->alias ? ' as ' . $node->alias : '');
         */
        // process the name
        $this->resolveNode($this->node->name, $treeNode);
        // process the alias, if needed
        if ($this->node->name->getLast() !== $this->node->alias) {
            /** @var Line $line */
            $line = $treeNode->getData()->line;
            $line->add(' as ')->add($this->node->alias);
        }
    }
}