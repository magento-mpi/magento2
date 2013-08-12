<?php
/**
 * Mage_Webhook_Model_Resource_Subscription_Grid_Collection
 *
 * We need DB isolation to avoid confusing interactions with the other Webhook tests.
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Subscription_Grid_CollectionTest extends PHPUnit_Framework_TestCase
{
    /** Topics */
    const TOPIC_LISTENERS_THREE = 'listeners/three';
    const TOPIC_LISTENERS_TWO = 'listeners/two';
    const TOPIC_LISTENERS_ONE = 'listeners/one';
    const TOPIC_UNKNOWN = 'unknown';

    /**
     * API Key for user
     */
    const API_KEY = 'Mage_Webhook_Model_Resource_Subscription_Grid_CollectionTest';

    /** @var int */
    private static $_apiUserId;

    /** @var Mage_Webhook_Model_Subscription[]  */
    private $_subscriptions;

    /** @var Mage_Webhook_Model_Subscription_Config */
    private $_config;

    public static function setUpBeforeClass()
    {
        /** @var Mage_Webapi_Model_Acl_User $user */
        $user = Mage::getObjectManager()->create('Mage_Webapi_Model_Acl_User');
        $user->loadByKey(self::API_KEY);
        if ($user->getId()) {
            self::$_apiUserId = $user->getId();
        } else {
            /** @var Mage_Webhook_Model_Webapi_User_Factory $webapiUserFactory */
            $webapiUserFactory = Mage::getObjectManager()->create('Mage_Webhook_Model_Webapi_User_Factory');
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
        $gridCollection = Mage::getObjectManager()
            ->create('Mage_Webhook_Model_Resource_Subscription_Grid_Collection',
                array('subscriptionConfig' => $this->_config));

        $subscriptions   = $gridCollection->getItems();
        $this->assertEquals(5, count($subscriptions));
    }

    /**
     * Create subscription configure
     *
     * @return Mage_Webhook_Model_Subscription_Config
     */
    protected function _createSubscriptionConfig()
    {
        $dirs = Mage::getObjectManager()->create(
            'Mage_Core_Model_Dir',
            array(
                'baseDir' => array(BP),
                'dirs' => array(Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files'),
            )
        );

        $modulesLoader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Loader_Modules',
            array('dirs' => $dirs)
        );

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Mage_Core_Model_Config_Cache',
            array('load', 'save', 'clean', 'getSection'),
            array(), '', false);

        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var Mage_Core_Model_Config_Storage $storage */
        $storage = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        $config = new Mage_Core_Model_Config_Base('<config />');
        $modulesLoader->load($config);

        /** @var Mage_Core_Model_Config_Modules $modulesConfig */
        $modulesConfig = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Modules', array(
                'storage' => $storage
            )
        );

        /** @var Mage_Core_Model_Config_Loader_Modules_File $fileReader */
        $fileReader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Loader_Modules_File', array(
                'dirs' => $dirs
            )
        );

        /** @var Mage_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Modules_Reader', array(
                'fileReader' => $fileReader,
                'modulesConfig' => $modulesConfig
            )
        );

        $mageConfig = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config',
            array('storage' => $storage, 'moduleReader' => $moduleReader)
        );

        /** @var Mage_Webhook_Model_Subscription_Config $config */
        return Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription_Config',
            array('mageConfig' => $mageConfig)
        );
    }

    protected function _createSubscriptions()
    {
        $this->_subscriptions = array();

        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/one/label', 'One Listener');
        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/two/label', 'Two Listeners');
        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/three/label', 'Three Listeners');

        /** @var Mage_Webhook_Model_Subscription $subscription */
        $subscription = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription');
        $subscription->setAlias('inactive')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/endpoint')
            ->setFormat('json')
            ->setName('Inactive Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_THREE))
            ->setStatus(Mage_Webhook_Model_Subscription::STATUS_INACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        /** @var Mage_Webhook_Model_Subscription $subscription */
        $subscription = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription');
        $subscription->setAlias('first')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/endpoint')
            ->setFormat('json')
            ->setName('First Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_THREE))
            ->setStatus(Mage_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        $subscription = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription');
        $subscription->setAlias('second')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/unique_endpoint')
            ->setFormat('json')
            ->setName('Second Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_TWO, self::TOPIC_LISTENERS_THREE))
            ->setStatus(Mage_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        $subscription = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription');
        $subscription->setAlias('third')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/unique_endpoint')
            ->setFormat('json')
            ->setName('Third Subscription')
            ->setTopics(array(
                self::TOPIC_LISTENERS_ONE,
                self::TOPIC_LISTENERS_TWO,
                self::TOPIC_LISTENERS_THREE))
            ->setStatus(Mage_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->setApiUserId(self::$_apiUserId)
            ->save();
        $this->_subscriptions[] = $subscription;
    }
}
