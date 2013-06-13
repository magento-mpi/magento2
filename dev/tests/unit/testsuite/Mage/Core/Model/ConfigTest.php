<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_model;

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
                        <di>
                            <Mage_Core_Model_Cache>
                                <parameters><one>two</one></parameters>
                            </Mage_Core_Model_Cache>
                        </di>
                    </global>
                </config>';

        $configBase = new Mage_Core_Model_Config_Base($xml);
        $objectManagerMock = $this->getMock('Magento_ObjectManager');
        $objectManagerMock->expects($this->once())->method('configure')->with(array(
            'Mage_Core_Model_Cache' => array(
                'parameters' => array('one' => 'two')
            )
        ));
        $appMock = $this->getMock('Mage_Core_Model_AppInterface');
        $configStorageMock = $this->getMock('Mage_Core_Model_Config_StorageInterface');
        $configStorageMock->expects($this->any())->method('getConfiguration')->will($this->returnValue($configBase));
        $modulesReaderMock = $this->getMock('Mage_Core_Model_Config_Modules_Reader', array(), array(), '', false);
        $invalidatorMock = $this->getMock('Mage_Core_Model_Config_InvalidatorInterface');

        $this->_model = new Mage_Core_Model_Config(
            $objectManagerMock, $configStorageMock, $appMock, $modulesReaderMock, $invalidatorMock
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
        /** @var Mage_Core_Model_Config_Element $tmp */
        $node = 'Module';
        $expected = array($node);

        $xpath = $this->_model->getXpath('global/resources/module_setup/setup/module');
        $xpath = $xpath; // PHPMD bug: unused local variable warning
        $this->assertEquals($expected, $xpath);
    }

    public function testSetNodeData()
    {
        $this->_model->setNode('modules/Module/active', 'true');

        /** @var Mage_Core_Model_Config_Element $tmp */
        $node = 'true';
        $expected = array($node);

        $actual = $this->_model->getXpath('modules/Module/active');
        $this->assertEquals($expected, $actual);
    }

    public function testGetNode()
    {
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $this->_model->getNode(
            'global/resources/module_setup/setup/module'));
    }

    public function testSetCurrentAreaCode()
    {
        $this->assertInstanceOf('Mage_Core_Model_Config', $this->_model->setCurrentAreaCode('adminhtml'));
    }

    public function testGetCurrentAreaCode()
    {
        $areaCode = 'adminhtml';
        $this->_model->setCurrentAreaCode($areaCode);
        $actual = $this->_model->getCurrentAreaCode();
        $this->assertEquals($areaCode, $actual);
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
        $this->_model->setCurrentAreaCode($areaCode);
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

    public function testGetModuleConfig()
    {
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $this->_model->getModuleConfig('Module'));
    }

    public function testIsModuleEnabled()
    {
        $this->_model->setNode('modules/Module/active', 'true');
        $this->assertEquals(true, $this->_model->isModuleEnabled('Module'));
    }
}
