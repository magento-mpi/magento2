<?php
/**
 * Magento_Webhook_Model_Subscription_Config
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Subscription_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * alias being used in the _files/config.xml file
     */
    const SUBSCRIPTION_ALIAS = 'subscription_alias';

    /**
     * name being used in the _files/config.xml file
     */
    const SUBSCRIPTION_NAME = 'Test subscriber';

    /**
     * @var Magento_Webhook_Model_Subscription_Config
     */
    private $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $dirs = $this->_objectManager->create(
            'Magento_Core_Model_Dir',
            array(
                'baseDir' => BP,
                'dirs'    => array(
                    Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files',
                    Magento_Core_Model_Dir::CONFIG => __DIR__ . '/_files'
                ),
            )
        );

        $fileResolver = $this->_objectManager->create(
            'Magento_Core_Model_Module_Declaration_FileResolver', array('applicationDirs' => $dirs)
        );
        $filesystemReader = $this->_objectManager->create('Magento_Core_Model_Module_Declaration_Reader_Filesystem',
            array('fileResolver' => $fileResolver)
        );
        $moduleList = $this->_objectManager->create(
            'Magento_Core_Model_ModuleList',
            array('reader' => $filesystemReader, 'cache' => $this->getMock("\Magento\Config\CacheInterface"))
        );
        $reader = $this->_objectManager->create(
            'Magento_Core_Model_Config_Modules_Reader', array('dirs' => $dirs, 'moduleList' => $moduleList)
        );
        $modulesLoader = $this->_objectManager->create(
            'Magento_Core_Model_Config_Loader_Modules', array('dirs' => $dirs, 'fileReader' => $reader)
        );

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Magento_Core_Model_Config_Cache', array('load', 'save', 'clean', 'getSection'),
            array(), '', false);

        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var Magento_Core_Model_Config_Storage $storage */
        $storage = $this->_objectManager->create(
            'Magento_Core_Model_Config_Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        /** @var Magento_Core_Model_Config_Modules $modulesConfig */
        $modulesConfig = $this->_objectManager->create(
            'Magento_Core_Model_Config_Modules', array(
                'storage' => $storage
            )
        );

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = $this->_objectManager->create(
            'Magento_Core_Model_Config_Modules_Reader', array(
                'dirs' => $dirs,
                'modulesConfig' => $modulesConfig
            )
        );

        $mageConfig = $this->_objectManager->create(
            'Magento_Core_Model_Config',
            array('storage' => $storage, 'moduleReader' => $moduleReader)
        );

        /** @var Magento_Webhook_Model_Subscription_Config $config */
        $this->_config = $this->_objectManager->create('Magento_Webhook_Model_Subscription_Config',
            array('mageConfig' => $mageConfig)
        );
    }

    public function testReadingConfig()
    {

        /** @var Magento_Webhook_Model_Resource_Subscription_Collection $subscriberCollection */
        $subscriptionSet = $this->_objectManager->create('Magento_Webhook_Model_Resource_Subscription_Collection');

        // Sanity check
        $subscriptions = $subscriptionSet->getSubscriptionsByAlias(self::SUBSCRIPTION_ALIAS);
        $this->assertEmpty($subscriptions);
        $this->_config->updateSubscriptionCollection();

        // Test that data matches what we have in config.xml
        $subscriptions = $subscriptionSet->getSubscriptionsByAlias(self::SUBSCRIPTION_ALIAS);
        $this->assertEquals(1, count($subscriptions));
        /** @var Magento_Webhook_Model_Subscription $subscription */
        $subscription = array_shift($subscriptions);
        $this->assertEquals(self::SUBSCRIPTION_NAME, $subscription->getName());
    }
}
