<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

interface AttributeOptionLabelInterface
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
