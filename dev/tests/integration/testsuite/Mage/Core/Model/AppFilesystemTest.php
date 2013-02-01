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

/**
 * Testing of the application interaction with the file system
 */
class Mage_Core_Model_AppFilesystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * Temporary directory with readonly permissions
     *
     * @var string
     */
    protected static $_tmpReadonlyDir;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        self::$_tmpReadonlyDir = Magento_Test_Helper_Bootstrap::getInstance()->getAppInstallDir() . '/' . __CLASS__;
        if (!is_dir(self::$_tmpReadonlyDir)) {
            mkdir(self::$_tmpReadonlyDir);
        }
        chmod(self::$_tmpReadonlyDir, 0444); // readonly permissions
    }

    public static function tearDownAfterClass()
    {
        rmdir(self::$_tmpReadonlyDir);
    }

    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_App');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    protected function assertPreConditions()
    {
        if (is_writable(self::$_tmpReadonlyDir)) {
            $this->markTestSkipped('Environment does not allow changing access permissions for files/directories.');
        }
    }

    /**
     * Test that application recognizes a provide path as a writable directory
     *
     * @param string $dirCode
     * @param string $path
     */
    protected function _testPathIsWritableDir($dirCode, $path)
    {
        $this->setExpectedException('Magento_BootstrapException', "Path '$path' has to be a writable directory.");
        $initParams = Magento_Test_Helper_Bootstrap::getInstance()->getAppInitParams();
        $initParams[Mage_Core_Model_App::INIT_OPTION_DIRS][$dirCode] = $path;
        $this->_model->baseInit($initParams);
    }

    /**
     * @param string $dirCode
     * @dataProvider writableDirCodeDataProvider
     * @magentoAppIsolation enabled
     */
    public function testInitFilesystemPathIsReadonlyDir($dirCode)
    {
        $this->_testPathIsWritableDir($dirCode, self::$_tmpReadonlyDir);
    }

    /**
     * @param string $dirCode
     * @dataProvider writableDirCodeDataProvider
     * @magentoAppIsolation enabled
     */
    public function testInitFilesystemPathIsExistingFile($dirCode)
    {
        $this->_testPathIsWritableDir($dirCode, __FILE__);
    }

    public function writableDirCodeDataProvider()
    {
        $result = array();
        foreach (Mage_Core_Model_Dir::getWritableDirCodes() as $dirCode) {
            $result[$dirCode] = array($dirCode);
        }
        return $result;
    }
}
