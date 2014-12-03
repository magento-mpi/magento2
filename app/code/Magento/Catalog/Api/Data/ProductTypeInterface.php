<?php
/**
 * Product type details
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

interface ProductTypeInterface
{
    /**
     * Get product type code
     *
     * @return string
     */
    public function getName();

    /**
     * Get product type label
     *
     * @return string
     */
    public function getLabel();
}
