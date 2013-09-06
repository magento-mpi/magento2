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

class Magento_Interception_Config_ConfigTest extends PHPUnit_Framework_TestCase
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
        $fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('di.xml', 'global', array($fixtureBasePath . '/Custom/Module/etc/di.xml')),
                array('di.xml', 'backend', array($fixtureBasePath . '/Custom/Module/etc/backend/di.xml')),
                array('di.xml', 'frontend', array($fixtureBasePath . '/Custom/Module/etc/frontend/di.xml')),
            )));

        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));

        $reader = new \Magento\ObjectManager\Config\Reader\Dom(
            $fileResolverMock,
            new \Magento\ObjectManager\Config\Mapper\Dom(),
            new Magento_ObjectManager_Config_SchemaLocator(),
            $validationStateMock
        );
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_configScopeMock->expects($this->any())
            ->method('getAllScopes')
            ->will($this->returnValue(array('global', 'backend', 'frontend')));
        $cacheMock = $this->getMock('Magento\Cache\FrontendInterface');
        // turn cache off
        $cacheMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        $omConfigMock = $this->getMock('Magento_ObjectManager_Config');
        $omConfigMock->expects($this->any())
            ->method('getInstanceType')
            ->will($this->returnArgument(0));
        $this->_model = new Magento_Interception_Config_Config(
            $reader,
            $this->_configScopeMock,
            $cacheMock,
            new Magento_ObjectManager_Relations_Runtime(),
            $omConfigMock,
            null,
            null,
            'interception'
        );
    }

    /**
     * @param boolean $expectedResult
     * @param string $type
     * @dataProvider hasPluginsDataProvider
     */
    public function testHasPlugins($expectedResult, $type)
    {
        $this->assertEquals($expectedResult, $this->_model->hasPlugins($type));
    }

    public function hasPluginsDataProvider()
    {
        return array(
            // item container has plugins only in the backend scope
            array(
                true,
                'Magento_Interception_Custom_Module_Model_ItemContainer',
            ),
            array(
                true,
                'Magento_Interception_Custom_Module_Model_Item',
            ),
            array(
                true,
                'Magento_Interception_Custom_Module_Model_Item_Enhanced',
            ),
            array(
                // the following model has only inherited plugins
                true,
                'Magento_Interception_Custom_Module_Model_ItemContainer_Enhanced',
            )
        );
    }
}
