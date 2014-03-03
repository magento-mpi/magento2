<?php
/**
 * Attribute mapper that is used to build frontend representation of attribute
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Entity\Product\Attribute\Group;

use Magento\Eav\Model\Entity\Attribute;

interface AttributeMapperInterface
{
    /**
     * Map attribute to presentation format
     *
     * @param Attribute $attribute
     * @return array
     */
    public function map(Attribute $attribute);
} 
