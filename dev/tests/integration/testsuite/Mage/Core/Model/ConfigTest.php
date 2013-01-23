<?php
/**
 * Integration test for Mage_Core_Model_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * First part of Mage_Core_Model_Config testing:
 * - general behaviour is tested
 *
 * @see Mage_Core_Model_ConfigFactoryTest
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Mage_Core_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Mage::app()->getCacheInstance()->banUse('config');
    }

    public function testGetResourceModel()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $this->assertInstanceOf('Mage_Core_Model_Resource_Config', $this->_createModel(true)->getResourceModel());
    }

    public function testInit()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $model = $this->_createModel();
        $this->assertFalse($model->getNode());
        $model->init();
        $this->assertInstanceOf('Varien_Simplexml_Element', $model->getNode());
    }

    public function testLoadBase()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $model = $this->_createModel();
        $this->assertFalse($model->getNode());
        $model->loadBase();
        $this->assertInstanceOf('Varien_Simplexml_Element', $model->getNode('global'));
    }

    /**
     * @param string $etcDir
     * @param array $configOptions
     * @param string $expectedValue
     * @param string $expectedNode
     * @dataProvider loadBaseLocalConfigDataProvider
     */
    public function testLoadBaseLocalConfig($etcDir, array $configOptions, $expectedValue,
        $expectedNode = 'global/resources/core_setup/connection/model'
    ) {
        $this->markTestIncomplete('MAGETWO-6406');
        $configOptions[Mage::PARAM_APP_DIRS] = array(
            Mage_Core_Model_Dir::CONFIG => __DIR__ . "/_files/local_config/{$etcDir}",
        );
        $model = $this->_createModelWithApp($configOptions);

        $model->loadBase();
        $this->assertInstanceOf('Varien_Simplexml_Element', $model->getNode($expectedNode));
        $this->assertEquals($expectedValue, (string)$model->getNode($expectedNode));
    }

    /**
     * @return array
     */
    public function loadBaseLocalConfigDataProvider()
    {
        $extraConfigData = '
            <root>
                <global>
                    <resources>
                        <core_setup>
                            <connection>
                                <model>overridden</model>
                            </connection>
                        </core_setup>
                    </resources>
                </global>
            </root>
        ';
        return array(
            'no local config file & no custom config file' => array(
                'no_local_config',
                array(),
                'b',
            ),
            'no local config file & custom config file' => array(
                'no_local_config',
                array(Mage::PARAM_CUSTOM_LOCAL_FILE => 'custom/local.xml'),
                'b',
            ),
            'no local config file & custom config data' => array(
                'no_local_config',
                array(Mage::PARAM_CUSTOM_LOCAL_CONFIG => $extraConfigData),
                'overridden',
            ),
            'local config file & no custom config file' => array(
                'local_config',
                array(),
                'local',
            ),
            'local config file & custom config file' => array(
                'local_config',
                array(Mage::PARAM_CUSTOM_LOCAL_FILE => 'custom/local.xml'),
                'custom',
            ),
            'local config file & prohibited custom config file' => array(
                'local_config',
                array(Mage::PARAM_CUSTOM_LOCAL_FILE => 'custom/prohibited.filename.xml'),
                'local',
            ),
            'local config file & custom config data' => array(
                'local_config',
                array(
                    Mage::PARAM_CUSTOM_LOCAL_CONFIG => $extraConfigData),
                'overridden',
            ),
            'local config file & custom config file & custom config data' => array(
                'local_config',
                array(
                    Mage::PARAM_CUSTOM_LOCAL_FILE => 'custom/local.xml',
                    Mage::PARAM_CUSTOM_LOCAL_CONFIG => $extraConfigData,
                ),
                'overridden',
            ),
        );
    }

    public function testLoadBaseInstallDate()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        if (date_default_timezone_get() != 'UTC') {
            $this->markTestSkipped('Test requires "UTC" to be the default timezone.');
        }

        $options = array(
            Mage::PARAM_CUSTOM_LOCAL_CONFIG
                => sprintf(Mage_Core_Model_Config_Primary::CONFIG_TEMPLATE_INSTALL_DATE, 'Fri, 21 Dec 2012 00:00:00 +0000')
        );
        $model = $this->_createModelWithApp($options);

        $model->loadBase();
        $this->assertEquals(1356048000, $model->getInstallDate());
    }

    public function testLoadBaseInstallDateInvalid()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $options = array(
            Mage::PARAM_CUSTOM_LOCAL_CONFIG
                => sprintf(Mage_Core_Model_Config_Primary::CONFIG_TEMPLATE_INSTALL_DATE, 'invalid')
        );
        $model = $this->_createModelWithApp($options);

        $model->loadBase();
        $this->assertEmpty($model->getInstallDate());
    }

    public function testLoadLocales()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $options = array(
            Mage::PARAM_APP_DIRS => array(
                Mage_Core_Model_Dir::LOCALE => __DIR__ . '/_files/locale',
            )
        );
        $model = $this->_createModelWithApp($options);

        $model->loadBase();
        $model->loadLocales();
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $model->getNode('global/locale'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testLoadModulesCache()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $options = array(
            Mage::PARAM_CUSTOM_LOCAL_CONFIG
                => sprintf(Mage_Core_Model_Config_Primary::CONFIG_TEMPLATE_INSTALL_DATE, 'Wed, 21 Nov 2012 03:26:00 +0000')
        );
        $model = $this->_createModelWithApp($options);

        Mage::app()->getCacheInstance()->allowUse('config');

        $model->loadBase();
        $this->assertTrue($model->loadModulesCache());
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $model->getNode());
    }

    public function testLoadModules()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $model = $this->_createModel();
        $model->loadBase();
        $this->assertFalse($model->getNode('modules'));
        $model->loadModules();
        $moduleNode = $model->getNode('modules/Mage_Core');
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $moduleNode);
        $this->assertTrue($moduleNode->is('active'));
    }

    public function testLoadModulesLocalConfigPrevails()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $options = array(
            Mage::PARAM_CUSTOM_LOCAL_CONFIG
                => '<config><modules><Mage_Core><active>false</active></Mage_Core></modules></config>'
        );
        $model = $this->_createModelWithApp($options);

        $model->loadBase();
        $model->loadModules();

        $moduleNode = $model->getNode('modules/Mage_Core');
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $moduleNode);
        $this->assertFalse($moduleNode->is('active'), 'Local configuration must prevail over modules configuration.');
    }

    public function testIsLocalConfigLoaded()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $model = $this->_createModel();
        $this->assertFalse($model->isLocalConfigLoaded());
        $model->loadBase();
        $this->assertTrue($model->isLocalConfigLoaded());
    }

    public function testReinitBaseConfig()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $options[Mage::PARAM_CUSTOM_LOCAL_CONFIG] = '<config><test>old_value</test></config>';

        $objectManager = new Magento_Test_ObjectManager(new Mage_Core_Model_ObjectManager_Config($options), BP);
        $model = $objectManager->get('Mage_Core_Model_Config');

        /** @var $app Mage_Core_Model_App */
        $app = $objectManager->get('Mage_Core_Model_App');

        $model->loadBase();
        $this->assertEquals('old_value', $model->getNode('test'));

        $options[Mage::PARAM_CUSTOM_LOCAL_CONFIG] = '<config><test>new_value</test></config>';
        $app->init($options);

        $model->reinit();
        $this->assertEquals('new_value', $model->getNode('test'));
    }

    public function testGetCache()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $this->assertInstanceOf('Varien_Cache_Core', $this->_createModel()->getCache());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSaveCache()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        Mage::app()->getCacheInstance()->allowUse('config');

        $model = $this->_createModel(true);
        $model->removeCache();
        $this->assertFalse($model->loadCache());

        $model->saveCache(array(Mage_Core_Model_Cache::OPTIONS_CACHE_ID));
        $this->assertTrue($model->loadCache());
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $model->getNode());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testRemoveCache()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        Mage::app()->getCacheInstance()->allowUse('config');

        $model = $this->_createModel();
        $model->removeCache();
        $this->assertFalse($model->loadCache());
    }

    public function testGetSectionNode()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $this->assertInstanceOf(
            'Mage_Core_Model_Config_Element', $this->_createModel(true)->getSectionNode(array('admin'))
        );
    }

    public function testGetNode()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $model = $this->_createModel();
        $this->assertFalse($model->getNode());
        $model->init();
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $model->getNode());
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $model->getNode(null, 'store', 1));
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $model->getNode(null, 'website', 1));
    }

    public function testSetNode()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $model = $this->_createModel(true);
        /* some existing node should be used */
        $model->setNode('admin/routers/adminhtml/use', 'test');
        $this->assertEquals('test', (string) $model->getNode('admin/routers/adminhtml/use'));
    }

    public function testDetermineOmittedNamespace()
    {
        $this->markTestIncomplete('MAGETWO-6406');

        $model = $this->_createModel(true);
        $this->assertEquals('cms', $model->determineOmittedNamespace('cms'));
        $this->assertEquals('Mage_Cms', $model->determineOmittedNamespace('cms', true));
        $this->assertEquals('', $model->determineOmittedNamespace('nonexistent'));
        $this->assertEquals('', $model->determineOmittedNamespace('nonexistent', true));
    }

    public function testGetDistroBaseUrl()
    {
        $_SERVER['SCRIPT_NAME'] = __FILE__;
        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertEquals('http://example.com/', $this->_createModel()->getDistroBaseUrl());
    }

    public function testGetModuleConfig()
    {
        $model = $this->_createModel(true);
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $model->getModuleConfig());
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $model->getModuleConfig('Mage_Core'));
    }

    public function testGetModuleDir()
    {
        $model = $this->_createModel(true);
        foreach (array('etc', 'controllers', 'sql', 'data', 'locale') as $type) {
            $dir = $model->getModuleDir($type, 'Mage_Core');
            $this->assertStringEndsWith($type, $dir);
            $this->assertContains('Mage' . DIRECTORY_SEPARATOR . 'Core', $dir);
        }
        $this->assertTrue(is_dir($this->_createModel(true)->getModuleDir('etc', 'Mage_Core')));
    }

    public function testGetPathVars()
    {
        $result = $this->_createModel()->getPathVars();
        $this->assertArrayHasKey('baseUrl', $result);
        $this->assertArrayHasKey('baseSecureUrl', $result);
    }

    public function testGetStoresConfigByPath()
    {
        $model = $this->_createModel(true);

        // default
        $baseUrl = $model->getStoresConfigByPath('web/unsecure/base_url');
        $this->assertArrayHasKey(0, $baseUrl);
        $this->assertArrayHasKey(1, $baseUrl);

        // $allowValues
        $baseUrl = $model->getStoresConfigByPath('web/unsecure/base_url', array(uniqid()));
        $this->assertEquals(array(), $baseUrl);

        // store code
        $baseUrl = $model->getStoresConfigByPath('web/unsecure/base_url', array(), 'code');
        $this->assertArrayHasKey('default', $baseUrl);
        $this->assertArrayHasKey('admin', $baseUrl);

        // store name
        $baseUrl = $model->getStoresConfigByPath('web/unsecure/base_url', array(), 'name');
        $this->assertArrayHasKey('Default Store View', $baseUrl);
        $this->assertArrayHasKey('Admin', $baseUrl);
    }

    /**
     * Test shouldUrlBeSecure() function for "Use Secure URLs in Frontend" = Yes
     *
     * @magentoConfigFixture current_store web/secure/use_in_frontend 1
     */
    public function testShouldUrlBeSecureWhenSecureUsedInFrontend()
    {
        $model = $this->_createModel(true);
        $this->assertFalse($model->shouldUrlBeSecure('/'));
        $this->assertTrue($model->shouldUrlBeSecure('/checkout/onepage'));
    }

    /**
     * Test shouldUrlBeSecure() function for "Use Secure URLs in Frontend" = No
     *
     * @magentoConfigFixture current_store web/secure/use_in_frontend 0
     */
    public function testShouldUrlBeSecureWhenSecureNotUsedInFrontend()
    {
        $model = $this->_createModel(true);
        $this->assertFalse($model->shouldUrlBeSecure('/'));
        $this->assertFalse($model->shouldUrlBeSecure('/checkout/onepage'));
    }



    /**
     * Instantiate Mage_Core_Model_Config and initialize (load configuration) if needed
     *
     * @param bool $initialize
     * @param array $arguments
     * @return Mage_Core_Model_Config
     */
    protected function _createModel($initialize = false, array $arguments = array())
    {
        /** @var $model Mage_Core_Model_Config */
        $model = Mage::getModel('Mage_Core_Model_Config', $arguments);
        return $model;
    }

    /**
     * @magentoAppIsolation enabled
     * @expectedException InvalidArgumentException
     */
    public function testGetAreaConfigThrowsExceptionIfNonexistentAreaIsRequested()
    {
        Mage::app()->getConfig()->getAreaConfig('non_existent_area_code');
    }

    /**
     * Check if areas loaded correctly from configuration
     */
    public function testGetAreas()
    {
        $model = $this->_createModel(true, array('sourceData' => __DIR__ . '/../_files/etc/config.xml'));

        $allowedAreas = $model->getAreas();
        $this->assertNotEmpty($allowedAreas, 'Areas are not initialized');

        $this->assertArrayHasKey('test_area1', $allowedAreas, 'Test area #1 is not loaded');

        $testAreaExpected = array(
            'base_controller' => 'Mage_Core_Controller_Varien_Action',
            'frontName' => 'TESTAREA',
            'routers'         => array(
                'test_router1' => array(
                    'class'   => 'Mage_Core_Controller_Varien_Router_Default'
                ),
                'test_router2' => array(
                    'class'   => 'Mage_Core_Controller_Varien_Router_Default'
                ),
            )
        );
        $this->assertEquals($testAreaExpected, $allowedAreas['test_area1'], 'Test area is not loaded correctly');

        $this->assertArrayNotHasKey('test_area2', $allowedAreas, 'Test area #2 is loaded by mistake');
        $this->assertArrayNotHasKey('test_area3', $allowedAreas, 'Test area #3 is loaded by mistake');
        $this->assertArrayNotHasKey('test_area4', $allowedAreas, 'Test area #4 is loaded by mistake');
        $this->assertArrayNotHasKey('test_area5', $allowedAreas, 'Test area #5 is loaded by mistake');
    }

    /**
     * Check if routers loaded correctly from configuration
     */
    public function testGetRouters()
    {
        $model = $this->_createModel(true, array('sourceData' => __DIR__ . '/../_files/etc/config.xml'));

        $loadedRouters = $model->getRouters();
        $this->assertArrayHasKey('test_router1', $loadedRouters, 'Test router #1 is not initialized in test area.');
        $this->assertArrayHasKey('test_router2', $loadedRouters, 'Test router #2 is not initialized in test area.');

        $testRouterExpected = array(
            'class'           => 'Mage_Core_Controller_Varien_Router_Default',
            'area'            => 'test_area1',
            'frontName'       => 'TESTAREA',
            'base_controller' => 'Mage_Core_Controller_Varien_Action'
        );
        $this->assertEquals($testRouterExpected, $loadedRouters['test_router1'], 'Test router is not loaded correctly');
        $this->assertEquals($testRouterExpected, $loadedRouters['test_router2'], 'Test router is not loaded correctly');
    }
}
