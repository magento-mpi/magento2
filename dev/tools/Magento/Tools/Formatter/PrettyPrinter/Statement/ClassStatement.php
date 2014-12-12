<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\ClassInterfaceLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class represents a class statement.
 */
class ClassStatement extends ClassTypeAbstract
{
    /**
     * This method constructs a new statement based on the specified class node.
     * @param \PHPParser_Node_Stmt_Class $node
     */
    public function __construct(\PHPParser_Node_Stmt_Class $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add the class line
        $this->addModifier($treeNode, $this->node->type);
        $this->addToLine($treeNode, 'class ')->add($this->node->name);
        // add in extends declaration
        if (!empty($this->node->extends)) {
            $this->addToLine($treeNode, ' extends ');
            $this->resolveNode($this->node->extends, $treeNode);
        }
        // add in the implement declarations
        if (!empty($this->node->implements)) {
            $this->addToLine($treeNode, ' implements');
            $this->processArgumentList($this->node->implements, $treeNode, new ClassInterfaceLineBreak());
        }
        $this->addToLine($treeNode, new HardLineBreak());
        return $this->addBody($treeNode);
    }
}
