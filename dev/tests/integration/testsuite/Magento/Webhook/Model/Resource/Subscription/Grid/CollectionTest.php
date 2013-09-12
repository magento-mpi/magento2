<?php
/**
 * Magento_Webhook_Model_Resource_Subscription_Grid_Collection
 *
 * We need DB isolation to avoid confusing interactions with the other Webhook tests.
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Subscription_Grid_CollectionTest extends PHPUnit_Framework_TestCase
{
    /** Topics */
    const TOPIC_LISTENERS_THREE = 'listeners/three';
    const TOPIC_LISTENERS_TWO = 'listeners/two';
    const TOPIC_LISTENERS_ONE = 'listeners/one';
    const TOPIC_UNKNOWN = 'unknown';

    /**
     * API Key for user
     */
    const API_KEY = 'Magento_Webhook_Model_Resource_Subscription_Grid_CollectionTest';

    /** @var int */
    private static $_apiUserId;

    /** @var Magento_Webhook_Model_Subscription[]  */
    private $_subscriptions;

    /** @var Magento_Webhook_Model_Subscription_Config */
    private $_config;

    public static function setUpBeforeClass()
    {
        /** @var Magento_Webapi_Model_Acl_User $user */
        $user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Webapi_Model_Acl_User');
        $user->loadByKey(self::API_KEY);
        if ($user->getId()) {
            self::$_apiUserId = $user->getId();
        } else {
            /** @var Magento_Webhook_Model_Webapi_User_Factory $webapiUserFactory */
            $webapiUserFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->create('Magento_Webhook_Model_Webapi_User_Factory');
            self::$_apiUserId = $webapiUserFactory->createUser(
                array(
                    'email'      => 'email@localhost.com',
                    'key'       => self::API_KEY,
                    'secret'    =>'secret'
                ),
                array()
            );
        }
    }

    public function setUp()
    {
        $this->_createSubscriptions();

        $this->_config = $this->_createSubscriptionConfig();
    }

    public function tearDown()
    {
        foreach ($this->_subscriptions as $subscription) {
            $subscription->delete();
        }
    }

    public function testGetSubscriptions()
    {
        $gridCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Resource_Subscription_Grid_Collection',
                array('subscriptionConfig' => $this->_config));

        $subscriptions   = $gridCollection->getItems();
        $this->assertEquals(5, count($subscriptions));
    }

    /**
     * Create subscription configure
     *
     * @return Magento_Webhook_Model_Subscription_Config
     */
    protected function _createSubscriptionConfig()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $dirs = $objectManager->create(
            'Magento_Core_Model_Dir',
            array(
                'baseDir' => BP,
                'dirs' => array(
                    Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files',
                    Magento_Core_Model_Dir::CONFIG => __DIR__ . '/_files',
                ),
            )
        );

        $moduleList = $objectManager->create('Magento_Core_Model_ModuleList', array(
            'reader' => $objectManager->create('Magento_Core_Model_Module_Declaration_Reader_Filesystem',
                array(
                    'fileResolver' => $objectManager->create(
                        'Magento_Core_Model_Module_Declaration_FileResolver',
                        array(
                            'applicationDirs' => $dirs
                        )
                    )
                )
            ),
            'cache' => $this->getMock('Magento_Config_CacheInterface')
        ));

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = $objectManager->create('Magento_Core_Model_Config_Modules_Reader', array(
            'dirs' => $dirs,
            'moduleList' => $moduleList
        ));

        /** @var Magento_Core_Model_Config_Loader $modulesLoader */
        $modulesLoader = $objectManager->create(
            'Magento_Core_Model_Config_Loader', array(
                'fileReader' => $moduleReader
        ));

        $config = new Magento_Core_Model_Config_Base('<config />');
        $modulesLoader->load($config);

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Magento_Core_Model_Config_Cache',
            array('load', 'save', 'clean', 'getSection'),
            array(), '', false);

        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var Magento_Core_Model_Config_Storage $storage */
        $storage = $objectManager->create(
            'Magento_Core_Model_Config_Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        /** @var Magento_Core_Model_Config $mageConfig */
        $mageConfig = $objectManager->create('Magento_Core_Model_Config', array(
            'storage' => $storage,
            'moduleReader' => $moduleReader,
            'moduleList' => $moduleList
        ));

        /** @var Magento_Webhook_Model_Subscription_Config $config */
        return $objectManager->create('Magento_Webhook_Model_Subscription_Config', array(
            'mageConfig' => $mageConfig
        ));
    }

    protected function _createSubscriptions()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_subscriptions = array();

        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/one/label', 'One Listener');
        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/two/label', 'Two Listeners');
        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/three/label', 'Three Listeners');

        /** @var Magento_Webhook_Model_Subscription $subscription */
        $subscription = $objectManager->create('Magento_Webhook_Model_Subscription');
        $subscription->setAlias('inactive')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/endpoint')
            ->setFormat('json')
            ->setName('Inactive Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_THREE))
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_INACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        /** @var Magento_Webhook_Model_Subscription $subscription */
        $subscription = $objectManager->create('Magento_Webhook_Model_Subscription');
        $subscription->setAlias('first')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/endpoint')
            ->setFormat('json')
            ->setName('First Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_THREE))
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        $subscription = $objectManager->create('Magento_Webhook_Model_Subscription');
        $subscription->setAlias('second')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/unique_endpoint')
            ->setFormat('json')
            ->setName('Second Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_TWO, self::TOPIC_LISTENERS_THREE))
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        $subscription = $objectManager->create('Magento_Webhook_Model_Subscription');
        $subscription->setAlias('third')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/unique_endpoint')
            ->setFormat('json')
            ->setName('Third Subscription')
            ->setTopics(array(
                self::TOPIC_LISTENERS_ONE,
                self::TOPIC_LISTENERS_TWO,
                self::TOPIC_LISTENERS_THREE))
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->setApiUserId(self::$_apiUserId)
            ->save();
        $this->_subscriptions[] = $subscription;
    }
}
