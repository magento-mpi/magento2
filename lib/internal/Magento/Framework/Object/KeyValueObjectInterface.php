<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Object;

interface KeyValueObjectInterface
{
    /**
     * Get object key
     *
     * @return string
     */
    public function getKey();

    /**
     * Get object value
     *
     * @return string
     */
    public function getValue();
}
