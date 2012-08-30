<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class MageTest extends PHPUnit_Framework_TestCase
{
    public function testIsInstalled()
    {
        $this->assertTrue(Mage::isInstalled());
    }

    /**
     * @magentoConfigFixture current_store dev/log/active 1
     * @link http://us3.php.net/manual/en/wrappers.php
     */
    public function testLogIntoWrapper()
    {
        $this->expectOutputRegex('/test/');
        Mage::log('test', null, 'php://output');
    }

    /**
     * @magentoConfigFixture current_store dev/log/active 1
     * @magentoConfigFixture global/log/core/writer_model Zend_Log_Writer_Mail
     */
    public function testLogStreamEnforced()
    {
        $logEntry = microtime();
        Mage::log($logEntry);
        $logFile = Mage::getBaseDir('log') . '/system.log';
        $this->assertFileExists($logFile);
        $this->assertContains($logEntry, file_get_contents($logFile));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testReset()
    {
        $logFile = Mage::getConfig()->getVarDir('log') . DIRECTORY_SEPARATOR . 'test.log';
        try {
            Mage::setRoot(dirname(__FILE__));
            $this->assertNotNull(Mage::getRoot());
            Mage::log('test message', null, 'test.log', true);
            $this->assertFileExists($logFile, "Could not write to '$logFile'");
            @unlink($logFile);
            $this->assertFileExists($logFile, "'$logFile' file was not locked by Mage::log() method and was removed");
            Mage::reset();
            $this->assertNull(Mage::getRoot());
            @unlink($logFile);
            $this->assertFileNotExists($logFile, "Could not delete '$logFile'. It is still locked.");
        } catch (Exception $e) {
            if (file_exists($logFile)) {
                unlink($logFile);
            }
            throw $e;
        }
    }

    /**
     * @magentoAppIsolation enabled
     *
     */
    public function testGetDesign()
    {
        $design = Mage::getDesign();
        $this->assertEquals('frontend', $design->getArea());
        $this->assertSame(Mage::getDesign(), $design);
    }

    /**
     * @param string $classId
     * @param string $expectedClassName
     * @dataProvider getModelDataProvider
     */
    public function testGetModel($classId, $expectedClassName)
    {
        $this->assertInstanceOf($expectedClassName, Mage::getModel($classId));
    }

    /**
     * @return array
     */
    public function getModelDataProvider()
    {
        return array(
            array('Mage_Core_Model_Config', 'Mage_Core_Model_Config')
        );
    }

    /**
     * @param string $classId
     * @param string $expectedClassName
     * @dataProvider getResourceModelDataProvider
     */
    public function testGetResourceModel($classId, $expectedClassName)
    {
        $this->assertInstanceOf($expectedClassName, Mage::getResourceModel($classId));
    }

    /**
     * @return array
     */
    public function getResourceModelDataProvider()
    {
        return array(
            array('Mage_Core_Model_Resource_Config', 'Mage_Core_Model_Resource_Config')
        );
    }

    /**
     * @param string $module
     * @param string $expectedClassName
     * @dataProvider getResourceHelperDataProvider
     */
    public function testGetResourceHelper($module, $expectedClassName)
    {
        $this->assertInstanceOf($expectedClassName, Mage::getResourceHelper($module));
    }

    /**
     * @return array
     */
    public function getResourceHelperDataProvider()
    {
        return array(
            array('Mage_Core', 'Mage_Core_Model_Resource_Helper_Abstract')
        );
    }

    /**
     * @param string $classId
     * @param string $expectedClassName
     * @dataProvider helperDataProvider
     */
    public function testHelper($classId, $expectedClassName)
    {
        $this->assertInstanceOf($expectedClassName, Mage::helper($classId));
    }

    /**
     * @return array
     */
    public function helperDataProvider()
    {
        return array(
            'module name' => array('Mage_Core',           'Mage_Core_Helper_Data'),
            'class name'  => array('Mage_Core_Helper_Js', 'Mage_Core_Helper_Js'),
        );
    }
}
