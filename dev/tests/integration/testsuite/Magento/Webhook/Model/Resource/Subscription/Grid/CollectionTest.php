<?php
/**
 * \Magento\Webhook\Model\Resource\Subscription\Grid\Collection
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
namespace Magento\Webhook\Model\Resource\Subscription\Grid;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** Topics */
    const TOPIC_LISTENERS_THREE = 'listeners/three';
    const TOPIC_LISTENERS_TWO = 'listeners/two';
    const TOPIC_LISTENERS_ONE = 'listeners/one';
    const TOPIC_UNKNOWN = 'unknown';

    /**
     * API Key for user
     */
    const API_KEY = 'Magento\Webhook\Model\Resource\Subscription\Grid\CollectionTest';

    /** @var int */
    private static $_apiUserId;

    /** @var \Magento\Webhook\Model\Subscription[]  */
    private $_subscriptions;

    /** @var \Magento\Webhook\Model\Subscription\Config */
    private $_config;

    public static function setUpBeforeClass()
    {
        /** @var \Magento\Webapi\Model\Acl\User $user */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Webapi\Model\Acl\User');
        $user->loadByKey(self::API_KEY);
        if ($user->getId()) {
            self::$_apiUserId = $user->getId();
        } else {
            /** @var \Magento\Webhook\Model\Webapi\User\Factory $webapiUserFactory */
            $webapiUserFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\Webhook\Model\Webapi\User\Factory');
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
        $gridCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Resource\Subscription\Grid\Collection',
                array('subscriptionConfig' => $this->_config));

        $subscriptions   = $gridCollection->getItems();
        $this->assertEquals(5, count($subscriptions));
    }

    /**
     * Create subscription configure
     *
     * @return \Magento\Webhook\Model\Subscription\Config
     */
    protected function _createSubscriptionConfig()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $dirs = $objectManager->create(
            'Magento\Core\Model\Dir',
            array(
                'baseDir' => BP,
                'dirs' => array(
                    \Magento\Core\Model\Dir::MODULES => __DIR__ . '/_files',
                    \Magento\Core\Model\Dir::CONFIG => __DIR__ . '/_files',
                ),
            )
        );

        $moduleList = $objectManager->create('Magento\Core\Model\ModuleList', array(
            'reader' => $objectManager->create('Magento\Core\Model\Module\Declaration\Reader\Filesystem',
                array(
                    'fileResolver' => $objectManager->create(
                        'Magento\Core\Model\Module\Declaration\FileResolver',
                        array(
                            'applicationDirs' => $dirs
                        )
                    )
                )
            ),
            'cache' => $this->getMock('Magento\Config\CacheInterface')
        ));

        /** @var \Magento\Core\Model\Config\Modules\Reader $moduleReader */
        $moduleReader = $objectManager->create('Magento\Core\Model\Config\Modules\Reader', array(
            'dirs' => $dirs,
            'moduleList' => $moduleList
        ));

        /** @var \Magento\Core\Model\Config\Loader $modulesLoader */
        $modulesLoader = $objectManager->create(
            'Magento\Core\Model\Config\Loader', array(
                'fileReader' => $moduleReader
        ));

        $config = new \Magento\Core\Model\Config\Base('<config />');
        $modulesLoader->load($config);

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var \PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Magento\Core\Model\Config\Cache',
            array('load', 'save', 'clean', 'getSection'),
            array(), '', false);

        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var \Magento\Core\Model\Config\Storage $storage */
        $storage = $objectManager->create(
            'Magento\Core\Model\Config\Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        /** @var \Magento\Core\Model\Config $mageConfig */
        $mageConfig = $objectManager->create('Magento\Core\Model\Config', array(
            'storage' => $storage,
            'moduleReader' => $moduleReader,
            'moduleList' => $moduleList
        ));

        /** @var \Magento\Webhook\Model\Subscription\Config $config */
        return $objectManager->create('Magento\Webhook\Model\Subscription\Config', array(
            'mageConfig' => $mageConfig
        ));
    }

    protected function _createSubscriptions()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_subscriptions = array();

        \Mage::getConfig()->setNode('global/webhook/webhooks/listeners/one/label', 'One Listener');
        \Mage::getConfig()->setNode('global/webhook/webhooks/listeners/two/label', 'Two Listeners');
        \Mage::getConfig()->setNode('global/webhook/webhooks/listeners/three/label', 'Three Listeners');

        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = $objectManager->create('Magento\Webhook\Model\Subscription');
        $subscription->setAlias('inactive')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/endpoint')
            ->setFormat('json')
            ->setName('Inactive Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_THREE))
            ->setStatus(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = $objectManager->create('Magento\Webhook\Model\Subscription');
        $subscription->setAlias('first')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/endpoint')
            ->setFormat('json')
            ->setName('First Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_THREE))
            ->setStatus(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        $subscription = $objectManager->create('Magento\Webhook\Model\Subscription');
        $subscription->setAlias('second')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/unique_endpoint')
            ->setFormat('json')
            ->setName('Second Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_TWO, self::TOPIC_LISTENERS_THREE))
            ->setStatus(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        $subscription = $objectManager->create('Magento\Webhook\Model\Subscription');
        $subscription->setAlias('third')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/unique_endpoint')
            ->setFormat('json')
            ->setName('Third Subscription')
            ->setTopics(array(
                self::TOPIC_LISTENERS_ONE,
                self::TOPIC_LISTENERS_TWO,
                self::TOPIC_LISTENERS_THREE))
            ->setStatus(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE)
            ->setApiUserId(self::$_apiUserId)
            ->save();
        $this->_subscriptions[] = $subscription;
    }
}
