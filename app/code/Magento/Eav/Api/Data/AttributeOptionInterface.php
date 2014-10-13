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
 * Created from:
 * @see \Magento\Catalog\Service\V1\Data\Eav\Option
 * @codeCoverageIgnore
 */
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
     * @return \Magento\Eav\Api\Data\Entity\Attribute\OptionLabel[]|null
     */
    public function getStoreLabels();
}
