<?php

/**
 * @property PHPParser_Node_Stmt_StaticVar[] $vars Variable definitions
 */
class PHPParser_Node_Stmt_Static extends PHPParser_Node_Stmt
{
    /**
     * Constructs a static variables list node.
     *
     * @param PHPParser_Node_Stmt_StaticVar[] $vars       Variable definitions
     * @param array                           $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = [])
    {
        parent::__construct(
            [
                'vars' => $vars,
            ],
            $attributes
        );
    }
}
