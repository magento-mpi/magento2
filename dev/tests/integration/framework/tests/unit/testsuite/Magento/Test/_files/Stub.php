<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_ClearProperties_Stub
{
    /**
     * @var boolean
     */
    public static $isDestructCalled = false;

    public function __destruct()
    {
        self::$isDestructCalled = true;
    }
}
