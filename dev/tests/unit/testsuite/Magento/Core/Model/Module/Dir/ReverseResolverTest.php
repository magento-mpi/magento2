<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Module\Dir;

class ReverseResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Module\Dir\ReverseResolver
     */
    protected $_model;

    /**
     * @var \Magento\App\ModuleListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleList;

    /**
     * @var \Magento\Core\Model\Module\Dir|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleDirs;

    protected function setUp()
    {
        $this->_moduleList = $this->getMock('Magento\App\ModuleListInterface');
        $this->_moduleDirs = $this->getMock('Magento\Core\Model\Module\Dir', array(), array(), '', false, false);
        $this->_model = new \Magento\Core\Model\Module\Dir\ReverseResolver($this->_moduleList, $this->_moduleDirs);
    }

    /**
     * @param string $path
     * @param string $expectedResult
     * @dataProvider getModuleNameDataProvider
     */
    public function testGetModuleName($path, $expectedResult)
    {
        $this->_moduleList
            ->expects($this->once())
            ->method('getModules')
            ->will($this->returnValue(array(
                'Fixture_ModuleOne' => array('name' => 'Fixture_ModuleOne'),
                'Fixture_ModuleTwo' => array('name' => 'Fixture_ModuleTwo'),
            )))
        ;
        $this->_moduleDirs
            ->expects($this->atLeastOnce())
            ->method('getDir')
            ->will($this->returnValueMap(array(
                array('Fixture_ModuleOne', '', 'app/code/Fixture/ModuleOne'),
                array('Fixture_ModuleTwo', '', 'app/code/Fixture/ModuleTwo'),
            )))
        ;
        $this->assertSame($expectedResult, $this->_model->getModuleName($path));
    }

    public function getModuleNameDataProvider()
    {
        return array(
            'module root dir' => array(
                'app/code/Fixture/ModuleOne', 'Fixture_ModuleOne'
            ),
            'module root dir trailing slash' => array(
                'app/code/Fixture/ModuleOne/', 'Fixture_ModuleOne'
            ),
            'module root dir backward slash' => array(
                'app/code\\Fixture\\ModuleOne', 'Fixture_ModuleOne'
            ),
            'dir in module' => array(
                'app/code/Fixture/ModuleTwo/etc', 'Fixture_ModuleTwo'
            ),
            'dir in module trailing slash' => array(
                'app/code/Fixture/ModuleTwo/etc/', 'Fixture_ModuleTwo'
            ),
            'dir in module backward slash' => array(
                'app/code/Fixture/ModuleTwo\\etc', 'Fixture_ModuleTwo'
            ),
            'file in module' => array(
                'app/code/Fixture/ModuleOne/etc/config.xml', 'Fixture_ModuleOne'
            ),
            'file in module backward slash' => array(
                'app\\code\\Fixture\\ModuleOne\\etc\\config.xml', 'Fixture_ModuleOne'
            ),
            'unknown module' => array(
                'app/code/Unknown/Module', null
            ),
        );
    }
}
