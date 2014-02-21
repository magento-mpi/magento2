<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Option;

/**
 * Option array interface
 */
interface ArrayInterface
{
    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray();
}
