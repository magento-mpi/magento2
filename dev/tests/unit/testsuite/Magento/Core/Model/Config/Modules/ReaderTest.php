<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Config_Modules_File
 */
class Magento_Core_Model_Config_Modules_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_protFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirsMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseConfigMock;

    protected function setUp()
    {
        $this->_protFactoryMock = $this->getMock('Magento_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_dirsMock = $this->getMock('Magento_Core_Model_Module_Dir', array(), array(), '', false, false);
        $this->_baseConfigMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_moduleListMock = $this->getMock('Magento_Core_Model_ModuleListInterface');

        $this->_model = new Magento_Core_Model_Config_Modules_Reader(
            $this->_dirsMock,
            $this->_protFactoryMock,
            $this->_moduleListMock
        );
    }

    public function testLoadModulesConfiguration()
    {
        $modulesConfig = array('mod1' => array());
        $fileName = 'acl.xml';
        $this->_protFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->with($this->equalTo('<config/>'))
            ->will($this->returnValue($this->_baseConfigMock));

        $this->_moduleListMock->expects($this->once())
            ->method('getModules')
            ->will($this->returnValue($modulesConfig));

        $result = $this->_model->loadModulesConfiguration($fileName, null, null, array());
        $this->assertInstanceOf('Magento_Core_Model_Config_Base', $result);
    }

    public function testLoadModulesConfigurationMergeToObject()
    {
        $fileName = 'acl.xml';
        $mergeToObject = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $mergeModel = null;
        $modulesConfig = array('mod1' => array());

        $this->_moduleListMock->expects($this->once())
            ->method('getModules')
            ->will($this->returnValue($modulesConfig));

        $this->_protFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo('<config/>'))
            ->will($this->returnValue($mergeToObject));

        $this->_model->loadModulesConfiguration($fileName, $mergeToObject, $mergeModel);
    }

    public function testGetModuleDir()
    {
        $expectedResult = new stdClass();
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->with('Test_Module', 'etc')
            ->will($this->returnValue($expectedResult));
        $this->assertSame($expectedResult, $this->_model->getModuleDir('etc', 'Test_Module'));

        // Custom value overrides the default one
        $this->_model->setModuleDir('Test_Module', 'etc', 'app/code/Test/Module/etc');
        $this->assertEquals('app/code/Test/Module/etc', $this->_model->getModuleDir('etc', 'Test_Module'));
    }
}
