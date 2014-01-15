<?php
/**
 * Generic test case for Web API functional tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\TestCase;

abstract class WebapiAbstract extends \PHPUnit_Framework_TestCase
{
    /** TODO: Reconsider implementation of fixture-management methods after implementing several tests */
    /**#@+
     * Auto tear down options in setFixture
     */
    const AUTO_TEAR_DOWN_DISABLED = 0;
    const AUTO_TEAR_DOWN_AFTER_METHOD = 1;
    const AUTO_TEAR_DOWN_AFTER_CLASS = 2;
    /**#@-*/

    /**#@+
     * Web API adapters that are used to perform actual calls.
     */
    const ADAPTER_SOAP = 'soap';
    const ADAPTER_REST = 'rest';
    /**#@-*/

    /**
     * Application cache model.
     *
     * @var \Magento\App\Cache
     */
    protected $_appCache;

    /**
     * The list of models to be deleted automatically in tearDown().
     *
     * @var array
     */
    protected $_modelsToDelete = array();

    /**
     * Namespace for fixtures is different for each test case.
     *
     * @var string
     */
    protected static $_fixturesNamespace;

    /**
     * The list of registered fixtures.
     *
     * @var array
     */
    protected static $_fixtures = array();

    /**
     * Fixtures to be deleted in tearDown().
     *
     * @var array
     */
    protected static $_methodLevelFixtures = array();

    /**
     * Fixtures to be deleted in tearDownAfterClass().
     *
     * @var array
     */
    protected static $_classLevelFixtures = array();

    /**
     * Original Magento config values.
     *
     * @var array
     */
    protected $_origConfigValues = array();

    /**
     * The list of instantiated Web API adapters.
     *
     * @var \Magento\TestFramework\TestCase\Webapi\AdapterInterface[]
     */
    protected $_webApiAdapters;

    /**
     * The list of available Web API adapters.
     *
     * @var array
     */
    protected $_webApiAdaptersMap = array(
        self::ADAPTER_SOAP => 'Magento\TestFramework\TestCase\Webapi\Adapter\Soap',
        self::ADAPTER_REST => 'Magento\TestFramework\TestCase\Webapi\Adapter\Rest'
    );

    /**
     * Initialize fixture namespaces.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::_setFixtureNamespace();
    }

    /**
     * Run garbage collector for cleaning memory
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        //clear garbage in memory
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
            gc_collect_cycles();
        }

        $fixtureNamespace = self::_getFixtureNamespace();
        if (isset(self::$_classLevelFixtures[$fixtureNamespace])
            && count(self::$_classLevelFixtures[$fixtureNamespace])
        ) {
            self::_deleteFixtures(self::$_classLevelFixtures[$fixtureNamespace]);
        }

        //ever disable secure area on class down
        self::_enableSecureArea(false);
        self::_unsetFixtureNamespace();
        parent::tearDownAfterClass();
    }

    /**
     * Call safe delete for models which added to delete list
     * Restore config values changed during the test
     *
     * @return void
     */
    protected function tearDown()
    {
        $fixtureNamespace = self::_getFixtureNamespace();
        if (isset(self::$_methodLevelFixtures[$fixtureNamespace])
            && count(self::$_methodLevelFixtures[$fixtureNamespace])
        ) {
            self::_deleteFixtures(self::$_methodLevelFixtures[$fixtureNamespace]);
        }
        $this->_callModelsDelete();
        $this->_restoreAppConfig();
        parent::tearDown();
    }

    /**
     * Perform Web API call to the system under test.
     *
     * @see \Magento\TestFramework\TestCase\Webapi\AdapterInterface::call()
     * @param array $serviceInfo
     * @param array $arguments
     * @param string|null $webApiAdapterCode
     * @return array Web API call results
     */
    protected function _webApiCall($serviceInfo, $arguments = array(), $webApiAdapterCode = null)
    {
        if (is_null($webApiAdapterCode)) {
            /** Default adapter code is defined in PHPUnit configuration */
            $webApiAdapterCode = strtolower(TESTS_WEB_API_ADAPTER);
        }
        return $this->_getWebApiAdapter($webApiAdapterCode)->call($serviceInfo, $arguments);
    }

    /**
     * Mark test to be executed for SOAP adapter only.
     */
    protected function _markTestAsSoapOnly($message = null)
    {
        if (TESTS_WEB_API_ADAPTER != self::ADAPTER_SOAP) {
            $this->markTestSkipped($message ? $message : "The test is intended to be executed for SOAP adapter only.");
        }
    }

    /**
     * Mark test to be executed for REST adapter only.
     */
    protected function _markTestAsRestOnly($message = null)
    {
        if (TESTS_WEB_API_ADAPTER != self::ADAPTER_REST) {
            $this->markTestSkipped($message ? $message : "The test is intended to be executed for REST adapter only.");
        }
    }

    /**
     * Set fixture to registry
     *
     * @param string $key
     * @param mixed $fixture
     * @param int $tearDown
     * @return void
     */
    public static function setFixture($key, $fixture, $tearDown = self::AUTO_TEAR_DOWN_AFTER_METHOD)
    {
        $fixturesNamespace = self::_getFixtureNamespace();
        if (!isset(self::$_fixtures[$fixturesNamespace])) {
            self::$_fixtures[$fixturesNamespace] = array();
        }
        self::$_fixtures[$fixturesNamespace][$key] = $fixture;
        if ($tearDown == self::AUTO_TEAR_DOWN_AFTER_METHOD) {
            if (!isset(self::$_methodLevelFixtures[$fixturesNamespace])) {
                self::$_methodLevelFixtures[$fixturesNamespace] = array();
            }
            self::$_methodLevelFixtures[$fixturesNamespace][] = $key;
        } else if ($tearDown == self::AUTO_TEAR_DOWN_AFTER_CLASS) {
            if (!isset(self::$_classLevelFixtures[$fixturesNamespace])) {
                self::$_classLevelFixtures[$fixturesNamespace] = array();
            }
            self::$_classLevelFixtures[$fixturesNamespace][] = $key;
        }
    }

    /**
     * Get fixture by key
     *
     * @param string $key
     * @return mixed
     */
    public static function getFixture($key)
    {
        $fixturesNamespace = self::_getFixtureNamespace();
        if (array_key_exists($key, self::$_fixtures[$fixturesNamespace])) {
            return self::$_fixtures[$fixturesNamespace][$key];
        }
        return null;
    }

    /**
     * Call safe delete for model
     *
     * @param \Magento\Core\Model\AbstractModel $model
     * @param bool $secure
     * @return \Magento\TestFramework\TestCase\WebapiAbstract
     */
    static public function callModelDelete($model, $secure = false)
    {
        if ($model instanceof \Magento\Core\Model\AbstractModel && $model->getId()) {
            if ($secure) {
                self::_enableSecureArea();
            }
            $model->delete();
            if ($secure) {
                self::_enableSecureArea(false);
            }
        }
    }

    /**
     * Call safe delete for model
     *
     * @param \Magento\Core\Model\AbstractModel $model
     * @param bool $secure
     * @return \Magento\TestFramework\TestCase\WebapiAbstract
     */
    public function addModelToDelete($model, $secure = false)
    {
        $this->_modelsToDelete[] = array(
            'model' => $model,
            'secure' => $secure
        );
        return $this;
    }

    /**
     * Get Web API adapter (create if requested one does not exist).
     *
     * @param string $webApiAdapterCode
     * @return \Magento\TestFramework\TestCase\Webapi\AdapterInterface
     * @throws \LogicException When requested Web API adapter is not declared
     */
    protected function _getWebApiAdapter($webApiAdapterCode)
    {
        if (!isset($this->_webApiAdapters[$webApiAdapterCode])) {
            if (!isset($this->_webApiAdaptersMap[$webApiAdapterCode])) {
                throw new \LogicException(sprintf(
                    'Declaration of the requested Web API adapter "%s" was not found.',
                    $webApiAdapterCode
                ));
            }
            $this->_webApiAdapters[$webApiAdapterCode] = new $this->_webApiAdaptersMap[$webApiAdapterCode];
        }
        return $this->_webApiAdapters[$webApiAdapterCode];
    }

    /**
     * Set fixtures namespace
     *
     * @throws \RuntimeException
     */
    protected static function _setFixtureNamespace()
    {
        if (!is_null(self::$_fixturesNamespace)) {
            throw new \RuntimeException('Fixture namespace is already set.');
        }
        self::$_fixturesNamespace = uniqid();
    }

    /**
     * Unset fixtures namespace
     */
    protected static function _unsetFixtureNamespace()
    {
        $fixturesNamespace = self::_getFixtureNamespace();
        unset(self::$_fixtures[$fixturesNamespace]);
        self::$_fixturesNamespace = null;
    }

    /**
     * Get fixtures namespace
     *
     * @throws \RuntimeException
     * @return string
     */
    protected static function _getFixtureNamespace()
    {
        $fixtureNamespace = self::$_fixturesNamespace;
        if (is_null($fixtureNamespace)) {
            throw new \RuntimeException('Fixture namespace must be set.');
        }
        return $fixtureNamespace;
    }

    /**
     * Enable secure/admin area
     *
     * @param bool $flag
     * @return void
     */
    static protected function _enableSecureArea($flag = true)
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $objectManager->get('Magento\Core\Model\Registry')->unregister('isSecureArea');
        if ($flag) {
            $objectManager->get('Magento\Core\Model\Registry')->register('isSecureArea', $flag);
        }
    }

    /**
     * Call delete models from list
     *
     * @return \Magento\TestFramework\TestCase\WebapiAbstract
     */
    protected function _callModelsDelete()
    {
        if ($this->_modelsToDelete) {
            foreach ($this->_modelsToDelete as $key => $modelData) {
                /** @var $model \Magento\Core\Model\AbstractModel */
                $model = $modelData['model'];
                $this->callModelDelete($model, $modelData['secure']);
                unset($this->_modelsToDelete[$key]);
            }
        }
        return $this;
    }

    /**
     * Check if all error messages are expected ones
     *
     * @param array $expectedMessages
     * @param array $receivedMessages
     */
    protected function _assertMessagesEqual($expectedMessages, $receivedMessages)
    {
        foreach ($receivedMessages as $message) {
            $this->assertContains($message, $expectedMessages, "Unexpected message: '$message'");
        }
        $expectedErrorsCount = count($expectedMessages);
        $this->assertCount($expectedErrorsCount, $receivedMessages, 'Invalid messages quantity received');
    }

    /**
     * Delete array of fixtures
     *
     * @param array $fixtures
     */
    protected static function _deleteFixtures($fixtures)
    {
        foreach ($fixtures as $fixture) {
            self::deleteFixture($fixture, true);
        }
    }

    /**
     * Delete fixture by key
     *
     * @param string $key
     * @param bool $secure
     * @return void
     */
    public static function deleteFixture($key, $secure = false)
    {
        $fixturesNamespace = self::_getFixtureNamespace();
        if (array_key_exists($key, self::$_fixtures[$fixturesNamespace])) {
            self::callModelDelete(self::$_fixtures[$fixturesNamespace][$key], $secure);
            unset(self::$_fixtures[$fixturesNamespace][$key]);
        }
    }

    /** TODO: Remove methods below if not used, otherwise fix them (after having some tests implemented)*/

    /**
     * Get application cache model
     *
     * @return \Magento\App\Cache
     */
    protected function _getAppCache()
    {
        if (null === $this->_appCache) {
            //set application path
            $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
            /** @var \Magento\Core\Model\Config $config */
            $config = $objectManager->get('Magento\Core\Model\Config');
            $options = $config->getOptions();
            $currentCacheDir = $options->getCacheDir();
            $currentEtcDir = $options->getEtcDir();
            /** @var \Magento\Filesystem $filesystem */
            $filesystem = $objectManager->get('Magento\Filesystem');
            $options->setCacheDir($filesystem->getPath(\Magento\Filesystem::ROOT) . '/var/cache');
            $options->setEtcDir($filesystem->getPath(\Magento\Filesystem::ROOT) . '/app/etc');

            $this->_appCache = $objectManager->get('Magento\App\Cache');

            //revert paths options
            $options->setCacheDir($currentCacheDir);
            $options->setEtcDir($currentEtcDir);
        }
        return $this->_appCache;
    }

    /**
     * Clean config cache of application
     *
     * @return bool
     */
    protected function _cleanAppConfigCache()
    {
        return $this->_getAppCache()->clean(\Magento\Core\Model\Config::CACHE_TAG);
    }

    /**
     * Update application config data
     *
     * @param string $path              Config path with the form "section/group/node"
     * @param string|int|null $value    Value of config item
     * @param bool $cleanAppCache       If TRUE application cache will be refreshed
     * @param bool $updateLocalConfig   If TRUE local config object will be updated too
     * @param bool $restore             If TRUE config value will be restored after test run
     * @return \Magento\TestFramework\TestCase\WebapiAbstract
     * @throws \RuntimeException
     */
    protected function _updateAppConfig(
        $path,
        $value,
        $cleanAppCache = true,
        $updateLocalConfig = false,
        $restore = false
    ) {
        list($section, $group, $node) = explode('/', $path);

        if (!$section || !$group || !$node) {
            throw new \RuntimeException(sprintf(
                'Config path must have view as "section/group/node" but now it "%s"',
                $path
            ));
        }

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $config \Magento\Backend\Model\Config */
        $config = $objectManager->create('Magento\Backend\Model\Config');
        $data[$group]['fields'][$node]['value'] = $value;
        $config->setSection($section)
            ->setGroups($data)
            ->save();

        if ($restore && !isset($this->_origConfigValues[$path])) {
            $this->_origConfigValues[$path] = (string) $objectManager->get('Magento\Core\Model\Config')
                ->getNode($path, 'default');
        }

        //refresh local cache
        if ($cleanAppCache) {
            if ($updateLocalConfig) {
                $objectManager->get('Magento\Core\Model\Config')->reinit();
                $objectManager->get('Magento\Core\Model\StoreManagerInterface')->reinitStores();
            }

            if (!$this->_cleanAppConfigCache()) {
                throw new \RuntimeException('Application configuration cache cannot be cleaned.');
            }
        }

        return $this;
    }

    /**
     * Restore config values changed during tests
     */
    protected function _restoreAppConfig()
    {
        foreach ($this->_origConfigValues as $configPath => $origValue) {
            $this->_updateAppConfig($configPath, $origValue, true, true);
        }
    }
}
