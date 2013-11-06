<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use PHPParser_Node_Scalar_MethodConst;

class MethodConstantReference extends AbstractScalarReference
{
    /**
     * This method constructs a new statement based on the specified string
     * @param PHPParser_Node_Scalar_MethodConst $node
     */
    public function __construct(PHPParser_Node_Scalar_MethodConst $node)
    {
        parent::__construct($node, '__METHOD__');
    }
}
