<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data\Product\Attribute;

interface AttributeFrontendLabelInterface 
{
    /**
     * Get store id value
     *
     * @return string
     */
    public function getStoreId();

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();
}
