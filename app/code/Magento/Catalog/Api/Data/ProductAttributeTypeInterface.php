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
    const TYPE = 'type';

    const LABEL = 'label';

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Get type label
     *
     * @return string
     */
    public function getLabel();
}
