<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview\Config\Data;

class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\Config\Data\Proxy
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Mview\Config\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock(
            'Magento\Framework\ObjectManager', array(), array(), '', false
        );
        $this->dataMock = $this->getMock(
            'Magento\Mview\Config\Data', array(), array(), '', false
        );
    }

    public function testMergeShared()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Mview\Config\Data')
            ->will($this->returnValue($this->dataMock));
        $this->dataMock->expects($this->once())
            ->method('merge')
            ->with(['some_config']);

        $this->model = new Proxy(
            $this->objectManagerMock,
            'Magento\Mview\Config\Data',
            true
        );

        $this->model->merge(['some_config']);
    }

    public function testMergeNonShared()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Mview\Config\Data')
            ->will($this->returnValue($this->dataMock));
        $this->dataMock->expects($this->once())
            ->method('merge')
            ->with(['some_config']);

        $this->model = new Proxy(
            $this->objectManagerMock,
            'Magento\Mview\Config\Data',
            false
        );

        $this->model->merge(['some_config']);
    }

    public function testGetShared()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Mview\Config\Data')
            ->will($this->returnValue($this->dataMock));
        $this->dataMock->expects($this->once())
            ->method('get')
            ->with('some_path', 'default')
            ->will($this->returnValue('some_value'));

        $this->model = new Proxy(
            $this->objectManagerMock,
            'Magento\Mview\Config\Data',
            true
        );

        $this->assertEquals('some_value', $this->model->get('some_path', 'default'));
    }

    public function testGetNonShared()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Mview\Config\Data')
            ->will($this->returnValue($this->dataMock));
        $this->dataMock->expects($this->once())
            ->method('get')
            ->with('some_path', 'default')
            ->will($this->returnValue('some_value'));

        $this->model = new Proxy(
            $this->objectManagerMock,
            'Magento\Mview\Config\Data',
            false
        );

        $this->assertEquals('some_value', $this->model->get('some_path', 'default'));
    }
}
