<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * This class represents a class statement.
 */
class ClassStatement extends StatementAbstract {
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_Node_Stmt_Class $node
     */
    public function __construct(\PHPParser_Node_Stmt_Class $node) {
        parent::__construct($node);
    }

    /**
     * This method is used to process the current node.
     *
     * @param Tree $tree
     */
    public function process(Tree $tree) {
        // TODO: fill in here
    }
}