<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Install_Model_InstallerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpDir = '';

    /**
     * @var string
     */
    protected static $_tmpConfigFile = '';

    /**
     * @var Magento_Install_Model_Installer
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        self::$_tmpDir = Mage::getBaseDir(Magento_Core_Model_Dir::VAR_DIR) . DIRECTORY_SEPARATOR . __CLASS__;
        self::$_tmpConfigFile = self::$_tmpDir . DIRECTORY_SEPARATOR . 'local.xml';
        mkdir(self::$_tmpDir);
    }

    public static function tearDownAfterClass()
    {
        Magento_Io_File::rmdirRecursive(self::$_tmpDir);
    }

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Install_Model_Installer');
    }

    /**
     * Emulate configuration directory for the installer config model.
     * Method usage should be accompanied with '@magentoAppIsolation enabled' because of the object manager pollution.
     *
     * @param string $dir
     */
    protected function _emulateInstallerConfigDir($dir)
    {
        $objectManager = Mage::getObjectManager();
        $installerConfig = new Magento_Install_Model_Installer_Config(
            $objectManager->get('Magento_Core_Model_Config'),
            new Magento_Core_Model_Dir(__DIR__, array(), array(Magento_Core_Model_Dir::CONFIG => $dir)),
            $objectManager->get('Magento_Core_Model_Config_Resource'),
            new Magento_Filesystem(new Magento_Filesystem_Adapter_Local())
        );
        $objectManager->addSharedInstance($installerConfig, 'Magento_Install_Model_Installer_Config');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateAdministrator()
    {
        $this->markTestIncomplete('Story bug MAGETWO-8593');
        $userName = 'installer_test';
        $userPassword = '123123q';
        $userData = array(
            'username'  => $userName,
            'firstname' => 'First Name',
            'lastname'  => 'Last Name',
            'email'     => 'installer_test@example.com',
        );

        /** @var $user Magento_User_Model_User */
        $user = Mage::getModel('Magento_User_Model_User');
        $user->loadByUsername($userName);
        $this->assertEmpty($user->getId());

        $this->_model->createAdministrator($userData + array('password' => $userPassword));

        $user->loadByUsername($userName);
        $this->assertNotEmpty($user->getId());
        $this->assertEquals($userData, array_intersect_assoc($user->getData(), $userData));
        $this->assertNotEmpty($user->getPassword(), 'Password hash is expected to be loaded.');
        $this->assertNotEquals(
            $userPassword, $user->getPassword(),
            'Original password should not be stored/loaded as is for security reasons.'
        );
        $this->assertInstanceOf('Magento_User_Model_Role', $user->getRole());
        $this->assertEquals(1, $user->getRole()->getId(), 'User has to have admin privileges.');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInstallEncryptionKey()
    {
        $this->_emulateInstallerConfigDir(self::$_tmpDir);

        $keyPlaceholder = Magento_Install_Model_Installer_Config::TMP_ENCRYPT_KEY_VALUE;
        $fixtureConfigData = "<key>$keyPlaceholder</key>";
        $expectedConfigData = '<key>d41d8cd98f00b204e9800998ecf8427e</key>';

        file_put_contents(self::$_tmpConfigFile, $fixtureConfigData);
        $this->assertEquals($fixtureConfigData, file_get_contents(self::$_tmpConfigFile));

        $this->_model->installEncryptionKey('d41d8cd98f00b204e9800998ecf8427e');
        $this->assertEquals($expectedConfigData, file_get_contents(self::$_tmpConfigFile));
    }

    /**
     * @magentoAppIsolation enabled
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Key must not exceed
     */
    public function testInstallEncryptionKeySizeViolation()
    {
        // isolate the application from the configuration pollution, if the test fails
        $this->_emulateInstallerConfigDir(self::$_tmpDir);

        $this->_model->installEncryptionKey(str_repeat('a', 57));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetValidEncryptionKey()
    {
        $validKey = 'abcdef1234567890';
        $this->assertEquals($validKey, $this->_model->getValidEncryptionKey($validKey));
    }

    /**
     * @magentoAppIsolation enabled
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Key must not exceed
     */
    public function testGetValidEncryptionKeySizeViolation()
    {
        $this->_model->getValidEncryptionKey(str_repeat('1', 57));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetValidEncryptionKeyRandom()
    {
        $actualKey = $this->_model->getValidEncryptionKey();
        $this->assertRegExp('/^[a-f0-9]{32}$/', $actualKey);
        $this->assertNotEquals($actualKey, $this->_model->getValidEncryptionKey());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
     */
    public function testFinish()
    {
        $this->_emulateInstallerConfigDir(self::$_tmpDir);
        $configFile = Magento_Test_Helper_Bootstrap::getInstance()->getAppInstallDir() . '/etc/local.xml';
        copy($configFile, self::$_tmpConfigFile);

        $this->_model->finish();

        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Mage::getModel('Magento_Core_Model_Cache_StateInterface');

        /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
        $cacheTypeList = Mage::getModel('Magento_Core_Model_Cache_TypeListInterface');
        $types = array_keys($cacheTypeList->getTypes());
        foreach ($types as $type) {
            $this->assertTrue(
                $cacheState->isEnabled($type),
                "'$type' cache type has not been enabled after installation"
            );
        }
    }
}
