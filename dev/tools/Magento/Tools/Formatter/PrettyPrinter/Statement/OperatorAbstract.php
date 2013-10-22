<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgedeon
 * Date: 10/21/13
 * Time: 4:36 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Magento\Tools\Formatter\PrettyPrinter\Statement;


use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;
use PHPParser_Node_Expr;

abstract class OperatorAbstract extends BaseAbstract {
    public abstract function operator();
    public abstract function associativity();
    public abstract function precedence();
    /**
     * Prints an expression node with the least amount of parentheses necessary to preserve the meaning.
     *
     * @param PHPParser_Node $node                Node to pretty print
     * @param int            $parentPrecedence    Precedence of the parent operator
     * @param int            $parentAssociativity Associativity of parent operator
     *                                            (-1 is left, 0 is nonassoc, 1 is right)
     * @param int            $childPosition       Position of the node relative to the operator
     *                                            (-1 is left, 1 is right)
     *
     * @return string The pretty printed node
     */
    /*
    protected function pPrec(PHPParser_Node $node, $parentPrecedence, $parentAssociativity, $childPosition) {
        $type = $node->getType();
        if (isset($this->precedenceMap[$type])) {
            $childPrecedence = $this->precedenceMap[$type][0];
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                return '(' . $this->{'p' . $type}($node) . ')';
            }
        }

        return $this->{'p' . $type}($node);
    }
    */
    protected function resolvePrecedence(PHPParser_Node $node, TreeNode $treeNode, $childPosition) {
        /** @var BaseAbstract $statement */
        $child = StatementFactory::getInstance()->getStatement($node);
        if ($child instanceof OperatorAbstract) {
            $childPrecedence = $child->precedence();
            $childAssociativity = $child->associativity();
            $parentPrecedence = $this->precedence();
            $parentAssociativity = $this->associativity();
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                $treeNode->getData()->add('(');
                $child->resolve($treeNode);
                $treeNode->getData()->add(')');
            }
            else {
                $child->resolve($treeNode);
            }
        }
        else {
            $child->resolve($treeNode);
        }
    }
}
