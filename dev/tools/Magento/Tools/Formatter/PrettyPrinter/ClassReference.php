<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;


use Magento\Tools\Formatter\Tree\Tree;

class ClassReference extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_Node_Stmt_Class $node
     */
    public function __construct(\PHPParser_Node_Name $node)
    {
        parent::__construct($node);
    }

    /**
     * This method is used to process the current node.
     *
     * @param Tree $tree
     */
    public function process(Tree $tree)
    {
        // add the name to the end of the current line
        $line = $tree->getCurrentNode()->getData();
        $line->add((string)$this->node);
    }
}
