<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use PHPParser_Node_Scalar_FileConst;

class FileConstantReference extends AbstractScalarReference
{
    /**
     * This method constructs a new statement based on the specified string
     * @param PHPParser_Node_Scalar_FileConst $node
     */
    public function __construct(PHPParser_Node_Scalar_FileConst $node)
    {
        parent::__construct($node, '__FILE__');
    }
}
