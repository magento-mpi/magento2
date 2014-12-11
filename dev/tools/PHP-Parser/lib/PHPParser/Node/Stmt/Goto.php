<?php

/**
 * @property string $name Name of label to jump to
 */
class PHPParser_Node_Stmt_Goto extends PHPParser_Node_Stmt
{
    /**
     * Constructs a goto node.
     *
     * @param string $name       Name of label to jump to
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $attributes = [])
    {
        parent::__construct(
            [
                'name' => $name,
            ],
            $attributes
        );
    }
}
