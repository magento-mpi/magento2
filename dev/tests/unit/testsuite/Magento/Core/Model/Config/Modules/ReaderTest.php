<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Config\Modules\File
 */
namespace Magento\Core\Model\Config\Modules;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_protFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirsMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseConfigMock;

    protected function setUp()
    {
        $this->_protFactoryMock = $this->getMock('Magento\Core\Model\Config\BaseFactory',
            array(), array(), '', false, false);
        $this->_dirsMock = $this->getMock('Magento\App\Module\Dir', array(), array(), '', false, false);
        $this->_baseConfigMock = $this->getMock('Magento\Core\Model\Config\Base', array(), array(), '', false, false);
        $this->_moduleListMock = $this->getMock('Magento\Module\ModuleListInterface');

        $this->_model = new \Magento\Core\Model\Config\Modules\Reader(
            $this->_dirsMock,
            $this->_moduleListMock
        );
    }

    public function testGetModuleDir()
    {
        $this->_dirsMock->expects($this->any())
            ->method('getDir')
            ->with('Test_Module', 'etc')
            ->will($this->returnValue('app/code/Test/Module/etc'));
        $this->assertEquals('app/code/Test/Module/etc', $this->_model->getModuleDir('etc', 'Test_Module'));
    }
}
