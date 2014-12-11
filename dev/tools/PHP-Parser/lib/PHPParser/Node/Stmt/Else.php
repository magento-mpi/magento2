<?php

/**
 * @property PHPParser_Node[] $stmts Statements
 */
class PHPParser_Node_Stmt_Else extends PHPParser_Node_Stmt
{
    /**
     * Constructs an else node.
     *
     * @param PHPParser_Node[] $stmts      Statements
     * @param array            $attributes Additional attributes
     */
    public function __construct(array $stmts = [], array $attributes = [])
    {
        parent::__construct(
            [
                'stmts' => $stmts,
            ],
            $attributes
        );
    }
}
