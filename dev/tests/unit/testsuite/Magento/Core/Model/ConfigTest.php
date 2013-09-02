<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var Magento_Core_Model_ModuleListInterface
     */
    protected $_moduleListMock;

    public function setUp()
    {
        $xml = '<config>
                    <modules>
                        <Module>
                            <version>1.6.0.0</version>
                            <active>false</active>
                        </Module>
                    </modules>
                    <global>
                        <areas>
                            <adminhtml>
                                <base_controller>base_controller</base_controller>
                                <routers>
                                    <admin>
                                        <class>class</class>
                                    </admin>
                                </routers>
                                <frontName>backend</frontName>
                            </adminhtml>
                        </areas>
                        <resources>
                            <module_setup>
                                <setup>
                                    <module>Module</module>
                                    <class>Module_Model_Resource_Setup</class>
                                </setup>
                            </module_setup>
                        </resources>
                    </global>
                </config>';

        $configBase = new Magento_Core_Model_Config_Base($xml);
        $objectManagerMock = $this->getMock('Magento_Core_Model_ObjectManager', array(), array(), '', false);
        $appMock = $this->getMock('Magento_Core_Model_AppInterface');
        $configStorageMock = $this->getMock('Magento_Core_Model_Config_StorageInterface');
        $configStorageMock->expects($this->any())->method('getConfiguration')->will($this->returnValue($configBase));
        $modulesReaderMock = $this->getMock('Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false);
        $invalidatorMock = $this->getMock('Magento_Core_Model_Config_InvalidatorInterface');
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_moduleListMock = $this->getMock('Magento_Core_Model_ModuleListInterface');
        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $this->_model = new Magento_Core_Model_Config(
            $objectManagerMock,
            $configStorageMock,
            $appMock,
            $modulesReaderMock,
            $this->_moduleListMock,
            $invalidatorMock,
            $this->_configScopeMock,
            $coreStoreConfig
        );
    }

    public function testGetXpathMissingXpath()
    {
        $xpath = $this->_model->getXpath('global/resources/module_setup/setup/module1');
        $xpath = $xpath; // PHPMD bug: unused local variable warning
        $this->assertEquals(false, $xpath);
    }

    public function testGetXpath()
    {
        /** @var Magento_Core_Model_Config_Element $tmp */
        $node = 'Module';
        $expected = array($node);

        $xpath = $this->_model->getXpath('global/resources/module_setup/setup/module');
        $xpath = $xpath; // PHPMD bug: unused local variable warning
        $this->assertEquals($expected, $xpath);
    }

    public function testSetNodeData()
    {
        $this->_model->setNode('some/custom/node', 'true');

        /** @var Magento_Core_Model_Config_Element $tmp */
        $node = 'true';
        $expected = array($node);

        $actual = $this->_model->getXpath('some/custom/node');
        $this->assertEquals($expected, $actual);
    }

    public function testGetNode()
    {
        $this->assertInstanceOf('Magento_Core_Model_Config_Element', $this->_model->getNode(
            'global/resources/module_setup/setup/module'));
    }

    public function testGetAreas()
    {
        $expected = array(
            'adminhtml' => array(
                'base_controller' => 'base_controller',
                'routers' => array(
                    'admin' => array(
                        'class' => 'class'
                    ),
                ),
                'frontName' => 'backend',
            ),
        );

        $areaCode = 'adminhtml';
        $this->_configScopeMock->expects($this->any())
            ->method('getCurrentScope')->will($this->returnValue($areaCode));
        $this->assertEquals($expected, $this->_model->getAreas());
    }

    public function testGetRouters()
    {
        $expected = array(
            'admin' => array(
                'class' => 'class',
                'base_controller' => 'base_controller',
                'frontName' => 'backend',
                'area' => 'adminhtml',
            ),
        );

        $this->assertEquals($expected, $this->_model->getRouters());
    }
}
