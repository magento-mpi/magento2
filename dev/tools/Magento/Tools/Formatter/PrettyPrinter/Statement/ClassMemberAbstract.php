<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Stmt;

abstract class ClassMemberAbstract extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Stmt $node
     */
    public function __construct(PHPParser_Node_Stmt $node)
    {
        parent::__construct($node);
        // Enable trimming blank lines around comments
        $this->trimComments = true;
    }
}
