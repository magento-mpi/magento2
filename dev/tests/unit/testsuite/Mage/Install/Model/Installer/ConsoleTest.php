<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Install
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Install_Model_Installer_ConsoleTest extends PHPUnit_Framework_TestCase
{
    public function testGenerateEncryptionKey()
    {
        /** @var $model Mage_Install_Model_Installer_Console */
        $model = $this->getMock('Mage_Install_Model_Installer_Console', null, array(), '', false);
        /** @var $helper Mage_Core_Helper_Data */
        $helper = $this->getMock('Mage_Core_Helper_Data', null, array(), '', false);

        $time1 = time();
        $key1 = $model->generateEncryptionKey($helper);
        $time2 = time();
        $key2 = $model->generateEncryptionKey($helper);
        $time3 = time();
        $key3 = $model->generateEncryptionKey($helper);
        if ($time1 != $time2 && $time2 != $time3) {
            $this->markTestSkipped('System is way too slow to perform this test.');
        }

        $this->assertNotEquals($key1, $key2);
        $this->assertNotEquals($key1, $key3);
        $this->assertNotEquals($key2, $key3);
    }
}
