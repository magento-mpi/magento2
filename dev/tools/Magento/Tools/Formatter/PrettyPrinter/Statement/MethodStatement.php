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
use Magento\Tools\Formatter\PrettyPrinter\ParameterLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_ClassMethod;

class MethodStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Stmt_ClassMethod $node
     */
    public function __construct(PHPParser_Node_Stmt_ClassMethod $node)
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
        // add the class line
        $line = new Line();
        $this->addModifier($this->node->type, $line);
        $line->add('function ');
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $treeNode->setData($line);
        if ($this->node->byRef) {
            $line->add('&');
        }
        // add in the parameters
        $lineBreak = new ParameterLineBreak();
        $line->add($this->node->name)->add('(');
        $this->processArgumentList($this->node->params, $treeNode, $line, $lineBreak);
        $line->add($lineBreak);
        $line->add(')')->add($lineBreak)->add('{')->add(new HardLineBreak());
        // process content of the methods
        $this->processNodes($this->node->stmts, $treeNode);
        // add closing block
        $treeNode->addSibling(new TreeNode((new Line('}'))->add(new HardLineBreak())));
    }

    /**
     * This method processes the newly added node.
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     * @return TreeNode Returns the originating node since just children are being added.
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total)
    {
        // this is called to add the member nodes to the class
        $originatingNode->addChild($newNode);
        // always return the originating node
        return $originatingNode;
    }
}