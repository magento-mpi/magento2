<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgedeon
 * Date: 10/22/13
 * Time: 12:26 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Minus;

class MinusOperator extends InfixOperatorAbstract {
    public function __construct(PHPParser_Node_Expr_Minus $node) {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' - ';
    }
    /* 'Expr_Minus'            => array( 5, -1), */
    public function associativity()
    {
        return -1;
    }
    public function precedence()
    {
        return 5;
    }
}
