<?php

/**
 * @property PHPParser_Node_Name[]                    $traits      Traits
 * @property PHPParser_Node_Stmt_TraitUseAdaptation[] $adaptations Adaptations
 */
class PHPParser_Node_Stmt_TraitUse extends PHPParser_Node_Stmt
{
    /**
     * Constructs a trait use node.
     *
     * @param PHPParser_Node_Name[]                    $traits      Traits
     * @param PHPParser_Node_Stmt_TraitUseAdaptation[] $adaptations Adaptations
     * @param array                                    $attributes  Additional attributes
     */
    public function __construct(array $traits, array $adaptations = [], array $attributes = [])
    {
        parent::__construct(
            [
                'traits'      => $traits,
                'adaptations' => $adaptations,
            ],
            $attributes
        );
    }
}
