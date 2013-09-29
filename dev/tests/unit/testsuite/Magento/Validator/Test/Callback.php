<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class with callback for testing callbacks
 */
namespace Magento\Validator\Test;

class Callback
{
    const ID = 3;

    /**
     * @return int
     */
    public function getId()
    {
        return self::ID;
    }

    /**
     * Fake method for testing callbacks
     */
    public function configureValidator()
    {
    }
}
