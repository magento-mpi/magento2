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
     * Temporary directory with write permissions
     *
     * @var string
     */
    protected static $_tmpWritableDir;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        $appInstallDir = Magento_Test_Helper_Bootstrap::getInstance()->getAppInstallDir();
        self::$_tmpReadonlyDir = $appInstallDir . DIRECTORY_SEPARATOR . __CLASS__ . '-readonly';
        self::$_tmpWritableDir = $appInstallDir . DIRECTORY_SEPARATOR . __CLASS__ . '-writable';

        foreach (array(self::$_tmpReadonlyDir => 0444, self::$_tmpWritableDir => 0777) as $tmpDir => $dirMode) {
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir);
            }
            chmod($tmpDir, $dirMode);
        }
    }

    public static function tearDownAfterClass()
    {
        foreach (array(self::$_tmpReadonlyDir, self::$_tmpWritableDir) as $tmpDir) {
            Varien_Io_File::chmodRecursive($tmpDir, 0777);
            Varien_Io_File::rmdirRecursive($tmpDir);
        }
    }

    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_App');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * Require the temporary fixture directory to be readonly, or skip the current test otherwise
     */
    protected function _requireReadonlyDir()
    {
        if (is_writable(self::$_tmpReadonlyDir)) {
            $this->markTestSkipped('Environment does not allow changing access permissions for files/directories.');
        }
    }

    /**
     * Initialize the application passing a custom directory path
     *
     * @param string $dirCode
     * @param string $path
     */
    protected function _initAppWithCustomDir($dirCode, $path)
    {
        $initParams = Magento_Test_Helper_Bootstrap::getInstance()->getAppInitParams();
        $initParams[Mage_Core_Model_App::INIT_OPTION_DIRS][$dirCode] = $path;
        $this->_model->baseInit($initParams);
    }

    /**
     * Setup expectation of the directory bootstrap exception
     *
     * @param string $path
     */
    protected function _expectDirBootstrapException($path)
    {
        $this->setExpectedException('Magento_BootstrapException', "Path '$path' has to be a writable directory.");
    }

    /**
     * @param string $dirCode
     * @dataProvider writableDirCodeDataProvider
     * @magentoAppIsolation enabled
     */
    public function testInitFilesystemExistingReadonlyDir($dirCode)
    {
        $this->_requireReadonlyDir();
        $this->_expectDirBootstrapException(self::$_tmpReadonlyDir);
        $this->_initAppWithCustomDir($dirCode, self::$_tmpReadonlyDir);
    }

    /**
     * @param string $dirCode
     * @dataProvider writableDirCodeDataProvider
     * @magentoAppIsolation enabled
     */
    public function testInitFilesystemExistingFile($dirCode)
    {
        $this->_expectDirBootstrapException(__FILE__);
        $this->_initAppWithCustomDir($dirCode, __FILE__);
    }

    /**
     * @param string $dirCode
     * @dataProvider writableDirCodeDataProvider
     * @magentoAppIsolation enabled
     */
    public function testInitFilesystemNewDirInReadonlyDir($dirCode)
    {
        $this->_requireReadonlyDir();
        $path = self::$_tmpReadonlyDir . DIRECTORY_SEPARATOR . 'non_existing_dir';
        $this->_expectDirBootstrapException($path);
        $this->_initAppWithCustomDir($dirCode, $path);
    }

    /**
     * @param string $dirCode
     * @dataProvider writableDirCodeDataProvider
     * @magentoAppIsolation enabled
     */
    public function testInitFilesystemNewDirInWritableDir($dirCode)
    {
        $path = self::$_tmpWritableDir . DIRECTORY_SEPARATOR . $dirCode;
        $this->assertFileNotExists($path);
        $this->_initAppWithCustomDir($dirCode, $path);
        $this->assertFileExists($path);
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
