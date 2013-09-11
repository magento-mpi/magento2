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
     * @var \Magento\Install\Model\Installer
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        self::$_tmpDir = Mage::getBaseDir(\Magento\Core\Model\Dir::VAR_DIR) . DIRECTORY_SEPARATOR . __CLASS__;
        self::$_tmpConfigFile = self::$_tmpDir . DIRECTORY_SEPARATOR . 'local.xml';
        mkdir(self::$_tmpDir);
    }

    public static function tearDownAfterClass()
    {
        \Magento\Io\File::rmdirRecursive(self::$_tmpDir);
    }

    protected function setUp()
    {
        $this->_model = Mage::getModel('\Magento\Install\Model\Installer');
    }

    /**
     * Emulate configuration directory for the installer config model.
     * Method usage should be accompanied with '@magentoAppIsolation enabled' because of the object manager pollution.
     *
     * @param string $dir
     */
    protected function _emulateInstallerConfigDir($dir)
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $installerConfig = new \Magento\Install\Model\Installer\Config(
            $objectManager->get('Magento\Core\Controller\Request\Http'),
            new \Magento\Core\Model\Dir(__DIR__, array(), array(\Magento\Core\Model\Dir::CONFIG => $dir)),
            $objectManager->get('Magento\Core\Model\Config\Resource'),
            new \Magento\Filesystem(new \Magento\Filesystem\Adapter\Local())
        );
        $objectManager->addSharedInstance($installerConfig, '\Magento\Install\Model\Installer\Config');
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

        /** @var $user \Magento\User\Model\User */
        $user = Mage::getModel('\Magento\User\Model\User');
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
        $this->assertInstanceOf('\Magento\User\Model\Role', $user->getRole());
        $this->assertEquals(1, $user->getRole()->getId(), 'User has to have admin privileges.');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInstallEncryptionKey()
    {
        $this->_emulateInstallerConfigDir(self::$_tmpDir);

        $keyPlaceholder = \Magento\Install\Model\Installer\Config::TMP_ENCRYPT_KEY_VALUE;
        $fixtureConfigData = "<key>$keyPlaceholder</key>";
        $expectedConfigData = '<key>d41d8cd98f00b204e9800998ecf8427e</key>';

        file_put_contents(self::$_tmpConfigFile, $fixtureConfigData);
        $this->assertEquals($fixtureConfigData, file_get_contents(self::$_tmpConfigFile));

        $this->_model->installEncryptionKey('d41d8cd98f00b204e9800998ecf8427e');
        $this->assertEquals($expectedConfigData, file_get_contents(self::$_tmpConfigFile));
    }

    /**
     * @magentoAppIsolation enabled
     * @expectedException \Magento\Exception
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
     * @expectedException \Magento\Exception
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
        $configFile = Magento_TestFramework_Helper_Bootstrap::getInstance()->getAppInstallDir() . '/etc/local.xml';
        copy($configFile, self::$_tmpConfigFile);

        $this->_model->finish();

        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = Mage::getModel('\Magento\Core\Model\Cache\StateInterface');

        /** @var \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList */
        $cacheTypeList = Mage::getModel('\Magento\Core\Model\Cache\TypeListInterface');
        $types = array_keys($cacheTypeList->getTypes());
        foreach ($types as $type) {
            $this->assertTrue(
                $cacheState->isEnabled($type),
                "'$type' cache type has not been enabled after installation"
            );
        }
    }
}
