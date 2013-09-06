<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
require_once __DIR__ . '/../Custom/Module/Model/Item.php';
require_once __DIR__ . '/../Custom/Module/Model/Item/Enhanced.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainer.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainer/Enhanced.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainerPlugin/Simple.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemPlugin/Simple.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemPlugin/Advanced.php';

class Magento_Interception_PluginList_PluginListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Interception_Config_Config
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    protected function setUp()
    {
        $fixtureBasePath = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/..');
        $fileResolverMock = $this->getMock('Magento_Config_FileResolverInterface');
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('di.xml', 'global', array($fixtureBasePath . '/Custom/Module/etc/di.xml')),
                array('di.xml', 'backend', array($fixtureBasePath . '/Custom/Module/etc/backend/di.xml')),
                array('di.xml', 'frontend', array($fixtureBasePath . '/Custom/Module/etc/frontend/di.xml')),
            )));

        $validationStateMock = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationStateMock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));

        $reader = new Magento_ObjectManager_Config_Reader_Dom(
            $fileResolverMock,
            new Magento_ObjectManager_Config_Mapper_Dom(),
            new Magento_ObjectManager_Config_SchemaLocator(),
            $validationStateMock
        );
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_configScopeMock->expects($this->any())
            ->method('getAllScopes')
            ->will($this->returnValue(array('global', 'backend', 'frontend')));
        $cacheMock = $this->getMock('Magento_Config_CacheInterface');
        // turn cache off
        $cacheMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));

        $omConfigMock = $this->getMock('Magento_ObjectManager_Config');
        $omConfigMock->expects($this->any())
            ->method('getInstanceType')
            ->will($this->returnArgument(0));
        $this->_model = new Magento_Interception_PluginList_PluginList(
            $reader,
            $this->_configScopeMock,
            $cacheMock,
            new Magento_ObjectManager_Relations_Runtime(),
            $omConfigMock,
            new Magento_Interception_Definition_Runtime(),
            array('global'),
            null,
            'interception'
        );
    }

    /**
     * @param array $expectedResult
     * @param string $type
     * @param string $method
     * @param string $scenario
     * @param string $scopeCode
     * @dataProvider getPluginsDataProvider
     */
    public function testGetPlugins(array $expectedResult, $type, $method, $scenario, $scopeCode)
    {
        $this->_configScopeMock->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue($scopeCode));
        $this->assertEquals(
            $expectedResult,
            $this->_model->getPlugins($type, $method, $scenario)
        );
    }

    /**
     * @return array
     */
    public function getPluginsDataProvider()
    {
        return array(
            array(
                array('Custom_Module_Model_ItemPlugin_Simple'),
                'Custom_Module_Model_Item',
                'getName',
                'after',
                'global',
            ),
            array(
                // advanced plugin has lower sort order
                array('Custom_Module_Model_ItemPlugin_Advanced', 'Custom_Module_Model_ItemPlugin_Simple'),
                'Custom_Module_Model_Item',
                'getName',
                'after',
                'backend',
            ),
            array(
                array('Custom_Module_Model_ItemPlugin_Advanced'),
                'Custom_Module_Model_Item',
                'getName',
                'around',
                'backend',
            ),
            array(
                // simple plugin is disabled in configuration for Custom_Module_Model_Item in frontend
                array(),
                'Custom_Module_Model_Item',
                'getName',
                'after',
                'frontend',
            ),
            // test plugin inheritance
            array(
                array('Custom_Module_Model_ItemPlugin_Simple'),
                'Custom_Module_Model_Item_Enhanced',
                'getName',
                'after',
                'global',
            ),
            array(
                // simple plugin is disabled in configuration for parent
                array('Custom_Module_Model_ItemPlugin_Advanced'),
                'Custom_Module_Model_Item_Enhanced',
                'getName',
                'after',
                'frontend',
            ),
            array(
                array('Custom_Module_Model_ItemPlugin_Advanced'),
                'Custom_Module_Model_Item_Enhanced',
                'getName',
                'around',
                'frontend',
            ),
            array(
                array(),
                'Custom_Module_Model_ItemContainer',
                'getName',
                'after',
                'global',
            ),
            array(
                array('Custom_Module_Model_ItemContainerPlugin_Simple'),
                'Custom_Module_Model_ItemContainer',
                'getName',
                'after',
                'backend',
            ),
        );
    }
}
