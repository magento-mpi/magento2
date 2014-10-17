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
 * Validation results interface.
 */
interface ValidationResults
{
    /**
     * Check if the provided data is valid.
     *
     * @return bool
     */
    public function isValid();

    /**
     * Get error messages as array in case of validation failure, else return empty array.
     *
     * @return string[]
     */
    public function getMessages();
}
