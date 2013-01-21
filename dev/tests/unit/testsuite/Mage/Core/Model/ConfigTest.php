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
        $this->markTestIncomplete('MAGETWO-6406');
        $xml = '<config>
                    <modules>
                        <Module>
                            <version>1.6.0.0</version>
                            <active>false</active>
                            <codePool>community</codePool>
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
                                <acl>
                                    <resourceLoader>resourceLoader</resourceLoader>
                                    <roleLocator>roleLocator</roleLocator>
                                    <policy>policy</policy>
                                </acl>
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

        $configBase = new Mage_Core_Model_Config_Base($xml);
        $objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $appMock = $this->getMock('Mage_Core_Model_AppInterface', array(), array(), '', false);
        $configStorageMock = $this->getMock('Mage_Core_Model_Config_StorageInterface', array(), array(), '', false);

        $configFactoryMock = $this->getMock('Mage_Core_Model_Config_Base_Factory', array(), array(), '', false);
        $configFactoryMock->expects($this->any())->method('create')->will($this->returnValue($configBase));

        $this->_model = new Mage_Core_Model_Config(
            $objectManagerMock, $dirMock, $configStorageMock, $configFactoryMock, $appMock);
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    public function testGetXpathMissingXpath()
    {
        $xpath = $this->_model->getXpath('global/resources/module_setup/setup/module1');
        $this->assertEquals(false, $xpath);
    }

    public function testGetXpath()
    {
        /** @var Mage_Core_Model_Config_Element $tmp */
        $node = 'Module';
        $expected = array( 0 => $node );

        $xpath = $this->_model->getXpath('global/resources/module_setup/setup/module');
        $this->assertEquals($expected, $xpath);
    }

    public function testSetNode()
    {
        $this->assertInstanceOf('Varien_Simplexml_Config', $this->_model->setNode(
        'modules/Module/active','true'));
    }

    public function testSetNodeData()
    {
        $this->_model->setNode('modules/Module/active','true');

        /** @var Mage_Core_Model_Config_Element $tmp */
        $node = 'true';
        $expected = array( 0 => $node );

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
                'acl' => array(
                    'resourceLoader' => 'resourceLoader',
                    'roleLocator' => 'roleLocator',
                    'policy' => 'policy',
                ),
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
                'acl' => array(
                    'resourceLoader' => 'resourceLoader',
                    'roleLocator' => 'roleLocator',
                    'policy' => 'policy',
                ),
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
        $this->_model->setNode('modules/Module/active','true');
        $this->assertEquals(true, $this->_model->isModuleEnabled('Module'));
    }
}
