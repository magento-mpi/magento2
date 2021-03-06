<?php

class PHPParser_Node_Scalar_DirConst extends PHPParser_Node_Scalar
{
    /**
     * Constructs a __DIR__ const node
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct([], $attributes);
    }
}
