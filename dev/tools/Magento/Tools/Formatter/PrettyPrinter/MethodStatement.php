<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

class MethodStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     */
    public function __construct(\PHPParser_Node_Stmt_ClassMethod $node)
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
        // predetermine parameters for the function
        $parameters = $this->getParametersForCall($node->params);
        $result = $this->pModifiers($node->type) . 'function ';
        if ($node->byRef) {
            $result .= '&';
        }
        $result .= $node->name . '(' . $parameters . ')';
        if (null !== $node->stmts) {
            // if the parameter span multiple lines, then start block on the same line; otherwise, start on new line
            if ($this->countNewlines($parameters) > 0) {
                $result .= ' ';
            } else {
                $result .= self::EOL;
            }
            $result .= '{' . self::EOL . $this->pStmts($node->stmts) . self::EOL . '}';
        } else {
            $result .= ';';
        }
        $result .= self::EOL;
        return $result;
         */
        // add the comments from the current node
        $this->addCommentsBefore($treeNode);
        // add the class line
        $line = new Line();
        $this->addModifier($this->node->type, $line);
        $line->add('function ');
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $treeNode->setData($line);
        if ($this->node->byRef) {
            $line->add('&');
        }
        $line->add($this->node->name)->add('(');
        // . $parameters .
        $line->add(')')->add(new HardLineBreak());
        $treeNode = $treeNode->addSibling(new TreeNode((new Line('{'))->add(new HardLineBreak())));
        // process statements
        // add closing block
        $treeNode->addSibling(new TreeNode((new Line('}'))->add(new HardLineBreak())));
    }
}