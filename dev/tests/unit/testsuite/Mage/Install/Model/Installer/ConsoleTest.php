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
        $helper = $this->getMock('Mage_Core_Helper_Data', array('getRandomString'), array(), '', false);
        $helper->expects($this->exactly(2))->method('getRandomString')->with(10)
            ->will($this->onConsecutiveCalls('1234567890', '0123456789'));
        $this->assertNotEquals($model->generateEncryptionKey($helper), $model->generateEncryptionKey($helper));
    }
}
