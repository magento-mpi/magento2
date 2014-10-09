<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data\Eav;

interface AttributeOptionInterface 
{
    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get option value
     *
     * @return string|null
     */
    public function getValue();

    /**
     * Get option order
     *
     * @return int|null
     */
    public function getOrder();

    /**
     * is default
     *
     * @return bool|null
     */
    public function isDefault();

    /**
     * Set option label for store scopes
     *
     * @return \Magento\Catalog\Api\Data\Eav\AttributeOptionLabel[]|null
     */
    public function getStoreLabels();
}
