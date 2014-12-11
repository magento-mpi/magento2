<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\ClassInterfaceLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Interface;

class InterfaceStatement extends ClassTypeAbstract
{
    /**
     * This method constructs a new statement based on the specified interface.
     * @param PHPParser_Node_Stmt_Interface $node
     */
    public function __construct(PHPParser_Node_Stmt_Interface $node)
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
        $this->addToLine($treeNode, 'interface ')->add($this->node->name);
        // add in extends declaration
        if (!empty($this->node->extends)) {
            $this->addToLine($treeNode, ' extends');
            $this->processArgumentList($this->node->extends, $treeNode, new ClassInterfaceLineBreak());
        }
        $this->addToLine($treeNode, new HardLineBreak());
        return $this->addBody($treeNode);
    }
}
