<?php

/**
 * @property string $var   Name of variable
 * @property bool   $byRef Whether to use by reference
 */
class PHPParser_Node_Expr_ClosureUse extends PHPParser_Node_Expr
{
    /**
     * Constructs a closure use node.
     *
     * @param string      $var        Name of variable
     * @param bool        $byRef      Whether to use by reference
     * @param array       $attributes Additional attributes
     */
    public function __construct($var, $byRef = false, array $attributes = [])
    {
        parent::__construct(
            [
                'var'   => $var,
                'byRef' => $byRef,
            ],
            $attributes
        );
    }
}
