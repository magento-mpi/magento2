<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\ConditionalLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardIndentLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_List;

class ListReference extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specify expression
     * @param PHPParser_Node_Expr_List $node
     */
    public function __construct(PHPParser_Node_Expr_List $node)
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
        $pList = array();
        foreach ($node->vars as $var) {
            if (null === $var) {
                $pList[] = '';
            } else {
                $pList[] = $this->p($var);
            }
        }
        return 'list(' . implode(', ', $pList) . ')';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        $line->add('list(');
        $lineBreak = new ConditionalLineBreak(array(array('', ' ')));
        $this->processArgumentList($this->node->vars, $treeNode, $line, $lineBreak);
        $line->add(')');
    }
}