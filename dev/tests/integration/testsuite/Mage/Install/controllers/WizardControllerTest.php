<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Install
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Install_WizardControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var string
     */
    protected static $_tmpMediaDir;

    /**
     * @var string
     */
    protected static $_tmpSkinDir;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$_tmpMediaDir = realpath(Magento_Test_Bootstrap::getInstance()->getTmpDir())
            . DIRECTORY_SEPARATOR . 'media';
        self::$_tmpSkinDir = self::$_tmpMediaDir . DIRECTORY_SEPARATOR . 'skin';
    }

    public function setUp()
    {
        parent::setUp();
        $this->_runOptions['is_installed'] = false;
    }

    public function tearDown()
    {
        Varien_Io_File::rmdirRecursive(self::$_tmpMediaDir);
        parent::tearDown();
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testPreDispatch()
    {
        $this->dispatch('install/index');
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }

    public function testPreDispatchNonWritableMedia()
    {
        mkdir(self::$_tmpMediaDir, 0444);
        $this->_runOptions['media_dir'] = self::$_tmpMediaDir;

        $this->_testInstallProhibitedWhenNonWritable(self::$_tmpMediaDir);
    }

    public function testPreDispatchNonWritableSkin()
    {
        mkdir(self::$_tmpMediaDir, 0777);
        $this->_runOptions['media_dir'] = self::$_tmpMediaDir;

        mkdir(self::$_tmpSkinDir, 0444);
        $this->_testInstallProhibitedWhenNonWritable(self::$_tmpSkinDir);
    }

    /**
     * Tests that when $nonWritableDir folder is read-only, the installation controller prohibits continuing
     * installation and points to fix issue with skin directory.
     *
     * @param string $nonWritableDir
     */
    protected function _testInstallProhibitedWhenNonWritable($nonWritableDir)
    {
        if (is_writable($nonWritableDir)) {
            $this->markTestSkipped("Current OS doesn't support setting write-access for folders via mode flags");
        }

        $this->dispatch('install/index');

        $this->assertEquals(503, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(self::$_tmpSkinDir, $this->getResponse()->getBody());
    }
}
