<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

/**
 * Validation rule interface.
 */
interface ValidationRuleInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const NAME = 'name';
    const VALUE = 'value';
    /**#@-*/

    /**
     * Get validation rule name
     *
     * @return string
     */
    public function getName();

    /**
     * Get validation rule value
     *
     * @return string
     */
    public function getValue();
}
