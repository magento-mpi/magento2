<?php

/**
 * @property PHPParser_Node_Expr $cond  Condition
 * @property PHPParser_Node[]    $stmts Statements
 */
class PHPParser_Node_Stmt_Do extends PHPParser_Node_Stmt
{
    /**
     * Constructs a do while node.
     *
     * @param PHPParser_Node_Expr $cond       Condition
     * @param PHPParser_Node[]    $stmts      Statements
     * @param array               $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $cond, array $stmts = [], array $attributes = [])
    {
        parent::__construct(
            [
                'cond'  => $cond,
                'stmts' => $stmts,
            ],
            $attributes
        );
    }
}
