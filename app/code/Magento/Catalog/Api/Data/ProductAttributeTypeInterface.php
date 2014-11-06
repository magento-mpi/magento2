<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

interface ProductAttributeTypeInterface
{
    const VALUE= 'value';

    const LABEL = 'label';

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Get type label
     *
     * @return string
     */
    public function getLabel();
}
