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

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sectionPoolMock;

    protected function setUp()
    {
        $xml = '<config>
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
        $configStorageMock = $this->getMock('Magento_Core_Model_Config_StorageInterface');
        $configStorageMock->expects($this->any())->method('getConfiguration')->will($this->returnValue($configBase));
        $modulesReaderMock = $this->getMock('Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_moduleListMock = $this->getMock('Magento_Core_Model_ModuleListInterface');
        $this->_sectionPoolMock = $this->getMock('Magento_Core_Model_Config_SectionPool', array(), array(), '', false);

        $this->_model = new Magento_Core_Model_Config(
            $objectManagerMock, $configStorageMock, $modulesReaderMock, $this->_moduleListMock,
            $this->_configScopeMock, $this->_sectionPoolMock
        );
    }
    public function testSetNodeData()
    {
        $this->_model->setNode('some/custom/node', 'true');

        $actual = (string)$this->_model->getNode('some/custom/node');
        $this->assertEquals('true', $actual);
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

    public function testSetValue()
    {
        $scope = 'default';
        $scopeCode = null;
        $value = 'test';
        $path = 'test/path';
        $sectionMock = $this->getMock('Magento_Core_Model_Config_Data', array(), array(), '', false);
        $this->_sectionPoolMock->expects($this->once())
            ->method('getSection')
            ->with($scope, $scopeCode)
            ->will($this->returnValue($sectionMock));
        $sectionMock->expects($this->once())
            ->method('setValue')
            ->with($path, $value);
        $this->_model->setValue($path, $value);
    }

    public function testGetValue()
    {
        $path = 'test/path';
        $scope = 'default';
        $scopeCode = null;
        $sectionMock = $this->getMock('Magento_Core_Model_Config_Data', array(), array(), '', false);
        $this->_sectionPoolMock->expects($this->once())->method('getSection')->with($scope, $scopeCode)
            ->will($this->returnValue($sectionMock));
        $sectionMock->expects($this->once())
            ->method('getValue')
            ->with($path);
        $this->_model->getValue($path);
    }

}
