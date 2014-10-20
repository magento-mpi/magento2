<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

/**
 * Interface AttributeGroupInterface must be implemented in \Magento\Eav\Model\Entity\Attribute\Group
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

    /**
     * Retrieve attribute set id
     *
     * @return int
     */
    public function getAttributeSetId();
}
