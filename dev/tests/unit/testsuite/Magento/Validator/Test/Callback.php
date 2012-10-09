<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class with callback for testing callbacks
 */
class Magento_Validator_Test_Callback
{
    /**
     * @return int
     */
    public function getId()
    {
        return 3;
    }

    /**
     * Fake method for testing callbacks
     */
    public function configureValidator()
    {
    }
}
