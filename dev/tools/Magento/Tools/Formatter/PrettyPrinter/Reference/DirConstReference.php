<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use PHPParser_Node_Scalar_DirConst;

class DirConstReference extends AbstractScalarReference
{
    /**
     * This method constructs a new statement based on the specified string
     * @param PHPParser_Node_Scalar_DirConst $node
     */
    public function __construct(PHPParser_Node_Scalar_DirConst $node)
    {
        parent::__construct($node, '__DIR__');
    }
}
