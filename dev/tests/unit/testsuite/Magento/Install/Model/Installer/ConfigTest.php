<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Model\Installer;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpConfigFile = '';

    /**
     * @var \Magento\Install\Model\Installer\Config
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
        $this->_model = new \Magento\Install\Model\Installer\Config(
            $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false),
            new \Magento\Core\Model\Dir(__DIR__, array(), array(\Magento\Core\Model\Dir::CONFIG => TESTS_TEMP_DIR)),
            new \Magento\Filesystem(new \Magento\Filesystem\Adapter\Local())
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testReplaceTmpInstallDate()
    {
        $datePlaceholder = \Magento\Install\Model\Installer\Config::TMP_INSTALL_DATE_VALUE;
        $fixtureConfigData = "<date>$datePlaceholder</date>";
        $expectedConfigData = '<date>Sat, 19 Jan 2013 18:50:39 -0800</date>';

        file_put_contents(self::$_tmpConfigFile, $fixtureConfigData);
        $this->assertEquals($fixtureConfigData, file_get_contents(self::$_tmpConfigFile));

        $this->_model->replaceTmpInstallDate('Sat, 19 Jan 2013 18:50:39 -0800');
        $this->assertEquals($expectedConfigData, file_get_contents(self::$_tmpConfigFile));
    }

    public function testReplaceTmpEncryptKey()
    {
        $keyPlaceholder = \Magento\Install\Model\Installer\Config::TMP_ENCRYPT_KEY_VALUE;
        $fixtureConfigData = "<key>$keyPlaceholder</key>";
        $expectedConfigData = '<key>3c7cf2e909fd5e2268a6e1539ae3c835</key>';

        file_put_contents(self::$_tmpConfigFile, $fixtureConfigData);
        $this->assertEquals($fixtureConfigData, file_get_contents(self::$_tmpConfigFile));

        $this->_model->replaceTmpEncryptKey('3c7cf2e909fd5e2268a6e1539ae3c835');
        $this->assertEquals($expectedConfigData, file_get_contents(self::$_tmpConfigFile));
    }
}
