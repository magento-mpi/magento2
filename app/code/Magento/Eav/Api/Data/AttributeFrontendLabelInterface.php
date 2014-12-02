<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

interface AttributeFrontendLabelInterface
{
    /**
     * Return store id
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Return label
     *
     * @return string|null
     */
    public function getLabel();
}
