<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_TestCase_FileSystemTest extends PHPUnit_Framework_TestCase
{
    public function testCreateDirInstance()
    {
        $object = new Magento_Test_Helper_FileSystem($this);
        $nonExistingDir = __DIR__ . DIRECTORY_SEPARATOR . 'non_existing';
        $dirInstance = $object->createDirInstance(__DIR__, array(), array(Mage_Core_Model_Dir::TMP => $nonExistingDir));

        $this->assertInstanceOf('Mage_Core_Model_Dir', $dirInstance);
        $this->assertEquals($dirInstance->getDir(Mage_Core_Model_Dir::ROOT), __DIR__);
        $this->assertEquals($dirInstance->getDir(Mage_Core_Model_Dir::TMP), $nonExistingDir);
    }
}
