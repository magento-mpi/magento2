<?php

class PHPParser_Node_Scalar_FuncConst extends PHPParser_Node_Scalar
{
    /**
     * Constructs a __FUNCTION__ const node
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct([], $attributes);
    }
}
