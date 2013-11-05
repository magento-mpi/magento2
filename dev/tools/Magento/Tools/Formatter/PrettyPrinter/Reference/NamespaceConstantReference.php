<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use PHPParser_Node_Scalar_NSConst;

class NamespaceConstantReference extends AbstractScalarReference
{
    /**
     * This method constructs a new statement based on the specified string
     * @param PHPParser_Node_Scalar_NSConst $node
     */
    public function __construct(PHPParser_Node_Scalar_NSConst $node)
    {
        parent::__construct($node, '__NAMESPACE__');
    }
}
