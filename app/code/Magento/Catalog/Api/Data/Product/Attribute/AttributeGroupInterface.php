<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data\Product\Attribute;

/**
 * Interface AttributeGroupInterface must be implemented in \Magento\Catalog\Model\Product\Attribute\Group
 */
interface AttributeGroupInterface 
{
    /**
     * Retrieve id
     *
     * @return string
     */
    public function getId();

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName();
}
