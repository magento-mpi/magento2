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
     * {@inheritdoc}
     */
    public function isValid()
    {
        return $this->_get(self::VALID);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->_get(self::MESSAGES);
    }
}
