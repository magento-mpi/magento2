<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group integrity
 */
class Integrity_Modular_SystemConfigFilesTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $fileList = glob(Mage::getBaseDir('app') . '/*/*/*/*/etc/adminhtml/system.xml');
        try {
            new Mage_Backend_Model_Config_Structure(array('sourceFiles' => $fileList));
        } catch (Magento_Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
