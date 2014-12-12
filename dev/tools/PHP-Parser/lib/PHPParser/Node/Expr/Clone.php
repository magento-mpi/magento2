<?php

/**
 * @property PHPParser_Node_Expr $expr Expression
 */
class PHPParser_Node_Expr_Clone extends PHPParser_Node_Expr
{
    /**
     * Constructs a clone node.
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
