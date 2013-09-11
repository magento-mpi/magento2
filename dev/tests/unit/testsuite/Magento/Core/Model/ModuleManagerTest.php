<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_ModuleManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * XPath in the configuration of a module output flag
     */
    const XML_PATH_OUTPUT_ENABLED = 'custom/is_module_output_enabled';

    /**
     * @var \Magento\Core\Model\ModuleManager
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_moduleList;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_storeConfig;

    protected function setUp()
    {
        $this->_moduleList = $this->getMockForAbstractClass('\Magento\Core\Model\ModuleListInterface');
        $this->_storeConfig = $this->getMockForAbstractClass('\Magento\Core\Model\Store\ConfigInterface');
        $this->_model = new \Magento\Core\Model\ModuleManager($this->_storeConfig, $this->_moduleList, array(
            'Module_DisabledOutputOne' => self::XML_PATH_OUTPUT_ENABLED,
            'Module_DisabledOutputTwo' => 'Magento_Core_Model_ModuleManagerTest::XML_PATH_OUTPUT_ENABLED',
        ));
    }

    public function testIsEnabledReturnsTrueForActiveModule()
    {
        $this->_moduleList->expects($this->once())->method('getModule')
            ->will($this->returnValue(array('name' => 'Some_Module')));
        $this->assertTrue($this->_model->isEnabled('Some_Module'));
    }

    public function testIsEnabledReturnsFalseForInactiveModule()
    {
        $this->_moduleList->expects($this->once())->method('getModule');
        $this->assertFalse($this->_model->isEnabled('Some_Module'));
    }

    public function testIsOutputEnabledReturnsFalseForDisabledModule()
    {
        $this->_storeConfig
            ->expects($this->any())
            ->method('getConfigFlag')
            ->will($this->returnValue(true));
        $this->assertFalse($this->_model->isOutputEnabled('Nonexisting_Module'));
    }

    /**
     * @param bool $configValue
     * @param bool $expectedResult
     * @dataProvider isOutputEnabledGenericConfigPathDataProvider
     */
    public function testIsOutputEnabledGenericConfigPath($configValue, $expectedResult)
    {
        $this->_moduleList->expects($this->any())->method('getModule')->will(
            $this->returnValue(array('name' => 'Module_EnabledOne'))
        );
        $this->_storeConfig
            ->expects($this->once())
            ->method('getConfigFlag')
            ->with('advanced/modules_disable_output/Module_EnabledOne')
            ->will($this->returnValue($configValue))
        ;
        $this->assertEquals($expectedResult, $this->_model->isOutputEnabled('Module_EnabledOne'));
    }

    public function isOutputEnabledGenericConfigPathDataProvider()
    {
        return array(
            'output disabled'   => array(true, false),
            'output enabled'    => array(false, true),
        );
    }

    /**
     * @param bool $configValue
     * @param string $moduleName
     * @param bool $expectedResult
     * @dataProvider isOutputEnabledCustomConfigPathDataProvider
     */
    public function testIsOutputEnabledCustomConfigPath($configValue, $moduleName, $expectedResult)
    {
        $this->_moduleList->expects($this->any())->method('getModule')->will(
            $this->returnValue(array('name' => $moduleName))
        );
        $this->_storeConfig
            ->expects($this->at(0))
            ->method('getConfigFlag')
            ->with(self::XML_PATH_OUTPUT_ENABLED)
            ->will($this->returnValue($configValue))
        ;
        $this->assertEquals($expectedResult, $this->_model->isOutputEnabled($moduleName));
    }

    public function isOutputEnabledCustomConfigPathDataProvider()
    {
        return array(
            'path literal, output disabled'     => array(false, 'Module_DisabledOutputOne', false),
            'path literal, output enabled'      => array(true, 'Module_DisabledOutputOne', true),
            'path constant, output disabled'    => array(false, 'Module_DisabledOutputTwo', false),
            'path constant, output enabled'     => array(true, 'Module_DisabledOutputTwo', true),
        );
    }
}
