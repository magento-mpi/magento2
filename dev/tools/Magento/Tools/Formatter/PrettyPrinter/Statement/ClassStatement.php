<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\ClassInterfaceLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class represents a class statement.
 */
class ClassStatement extends ClassTypeAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_Node_Stmt_Class $node
     */
    public function __construct(\PHPParser_Node_Stmt_Class $node)
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
        // add the class line
        $line = new Line();
        $this->addModifier($this->node->type, $line);
        $line->add('class ')->add($this->node->name);
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $treeNode->setData($line);
        // add in extends declaration
        if (!empty($this->node->extends)) {
            $line->add(' extends ');
            $this->resolveNode($this->node->extends, $treeNode);
        }
        // add in the implement declarations
        if (!empty($this->node->implements)) {
            $line->add(' implements');
            $this->processArgumentList($this->node->implements, $treeNode, $line, new ClassInterfaceLineBreak());
        }
        $line->add(new HardLineBreak());
        $this->addBody($treeNode);
    }
}
