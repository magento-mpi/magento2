<?php

/**
 * @property PHPParser_Node_Expr $expr Expression
 */
class PHPParser_Node_Stmt_Throw extends PHPParser_Node_Stmt
{
    /**
     * Constructs a throw node.
     *
     * @param PHPParser_Node_Expr $expr       Expression
     * @param array               $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $expr, array $attributes = [])
    {
        parent::__construct(
            [
                'expr' => $expr,
            ],
            $attributes
        );
    }
}
