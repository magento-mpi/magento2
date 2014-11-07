<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

interface AttributeSetInterface
{
    /**
     * Get attribute set ID
     *
     * @return int
     */
    public function getAttributeSetId();

    /**
     * Get attribute set name
     *
     * @return string
     */
    public function getAttributeSetName();

    /**
     * Get attribute set sort order index
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Get attribute set entity type id
     *
     * @return int
     */
    public function getEntityTypeId();
}
