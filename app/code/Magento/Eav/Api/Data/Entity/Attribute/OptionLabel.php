<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data\Entity\Attribute;

interface OptionLabel
{
    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();
}
