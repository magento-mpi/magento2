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
     * Retrieve product type name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve product type label
     *
     * @return string
     */
    public function getLabel();
}
