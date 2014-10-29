<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option\Type\File;

class ValidateFactory
{
    public function create()
    {
        return new \Zend_Validate();
    }
}
