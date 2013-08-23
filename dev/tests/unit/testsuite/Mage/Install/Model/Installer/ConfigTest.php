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

class Mage_Install_Model_Installer_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpConfigFile = '';

    /**
     * @var Mage_Install_Model_Installer_Config
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        self::$_tmpConfigFile = TESTS_TEMP_DIR . DIRECTORY_SEPARATOR . 'local.xml';
    }

    public static function tearDownAfterClass()
    {
        if (file_exists(self::$_tmpConfigFile)) {
            unlink(self::$_tmpConfigFile);
        }
    }

    protected function setUp()
    {
        $this->_model = new Mage_Install_Model_Installer_Config(
            $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false),
            new Mage_Core_Model_Dir(__DIR__, array(), array(Mage_Core_Model_Dir::CONFIG => TESTS_TEMP_DIR)),
            $this->getMock('Mage_Core_Model_Config_Resource', array(), array(), '', false),
            new Magento_Filesystem(new Magento_Filesystem_Adapter_Local())
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testReplaceTmpInstallDate()
    {
        $datePlaceholder = Mage_Install_Model_Installer_Config::TMP_INSTALL_DATE_VALUE;
        $fixtureConfigData = "<date>$datePlaceholder</date>";
        $expectedConfigData = '<date>Sat, 19 Jan 2013 18:50:39 -0800</date>';

        file_put_contents(self::$_tmpConfigFile, $fixtureConfigData);
        $this->assertEquals($fixtureConfigData, file_get_contents(self::$_tmpConfigFile));

        $this->_model->replaceTmpInstallDate('Sat, 19 Jan 2013 18:50:39 -0800');
        $this->assertEquals($expectedConfigData, file_get_contents(self::$_tmpConfigFile));
    }

    public function testReplaceTmpEncryptKey()
    {
        $keyPlaceholder = Mage_Install_Model_Installer_Config::TMP_ENCRYPT_KEY_VALUE;
        $fixtureConfigData = "<key>$keyPlaceholder</key>";
        $expectedConfigData = '<key>3c7cf2e909fd5e2268a6e1539ae3c835</key>';

        file_put_contents(self::$_tmpConfigFile, $fixtureConfigData);
        $this->assertEquals($fixtureConfigData, file_get_contents(self::$_tmpConfigFile));

        $this->_model->replaceTmpEncryptKey('3c7cf2e909fd5e2268a6e1539ae3c835');
        $this->assertEquals($expectedConfigData, file_get_contents(self::$_tmpConfigFile));
    }
}
