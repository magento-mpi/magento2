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
use PHPParser_Node_Stmt_Interface;

class InterfaceStatement extends ClassTypeAbstract
{
    /**
     * This method constructs a new statement based on the specify interface node
     * @param PHPParser_Node_Stmt_Interface $node
     */
    public function __construct(PHPParser_Node_Stmt_Interface $node)
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
        // add the class line
        $line->add('interface ')->add($this->node->name);
        // add in extends declaration
        if (!empty($this->node->extends)) {
            $line->add(' extends');
            $this->processArgumentList($this->node->extends, $treeNode, $line, new ClassInterfaceLineBreak());
        }
        $line->add(new HardLineBreak());
        $this->addBody($treeNode);
    }
}
