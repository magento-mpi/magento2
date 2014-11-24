<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

interface ProductLinkTypeInterface
{
    /**
     * Get link type code
     *
     * @return int
     */
    public function getCode();

    /**
     * Get link type name
     *
     * @return string
     */
    public function getName();
}
