<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

interface AttributeSetInterface
{
    /**
     * Get attribute set id
     *
     * @return int
     */
    public function getId();

    /**
     * Get attribute set name
     *
     * @return string
     */
    public function getName();

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