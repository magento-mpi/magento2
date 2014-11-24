<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Api\Data;

interface OptionTypeInterface
{
    /**
     * Get type label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get type code
     *
     * @return string
     */
    public function getCode();
}
