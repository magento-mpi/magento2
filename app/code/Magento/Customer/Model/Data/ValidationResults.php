<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Data;

/**
 * Validation results data model.
 */
class ValidationResults extends \Magento\Framework\Service\Data\AbstractExtensibleObject
    implements \Magento\Customer\Api\Data\ValidationResultsInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const VALID = 'valid';
    const MESSAGES = 'messages';

    /**
     * Check if the provided data is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->_get(self::VALID);
    }

    /**
     * Get error messages as array in case of validation failure, else return empty array.
     *
     * @return string[]
     */
    public function getMessages()
    {
        return $this->_get(self::MESSAGES);
    }
}
