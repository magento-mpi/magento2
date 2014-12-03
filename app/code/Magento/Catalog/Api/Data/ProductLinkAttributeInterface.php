<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

interface ProductLinkAttributeInterface
{
    /**
     * Get attribute code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get attribute type
     *
     * @return string
     */
    public function getType();
}
