<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Validator\Test;

/**
 * Test validator that always returns TRUE
 */
class True extends \Magento\Framework\Validator\AbstractValidator
{
    /**
     * Validate value
     *
     * @param mixed $value
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isValid($value)
    {
        return true;
    }
}
