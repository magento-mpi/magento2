<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

interface ProductCustomOptionOptionTypeInterface
{
    /**
     * Get option type label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get option type code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get option type group
     *
     * @return string
     */
    public function getGroup();
}
