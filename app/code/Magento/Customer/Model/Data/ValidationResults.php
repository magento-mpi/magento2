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
class ValidationResults extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Magento\Customer\Api\Data\ValidationResultsInterface
{
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
