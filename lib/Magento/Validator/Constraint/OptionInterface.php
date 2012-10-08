<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Validator Constraint Option interface
 */
interface Magento_Validator_Constraint_OptionInterface
{
    /**
     * Get option value
     *
     * @abstract
     * @return mixed
     */
    public function getValue();
}
