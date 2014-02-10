<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Mview\View\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewsFactoryMock;

    protected function setUp()
    {
        $this->viewsFactoryMock = $this->getMock(
            'Magento\Mview\View\CollectionFactory', array('create'), array(), '', false
        );
        $this->model = new \Magento\Mview\Processor($this->viewsFactoryMock);
    }

    /**
     * Return array of mocked views
     *
     * @param string $method
     * @return \Magento\Mview\ViewInterface[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected function getViews($method)
    {
        $viewMock = $this->getMock('Magento\Mview\ViewInterface', array(), array(), '', false);
        $viewMock->expects($this->exactly(2))
            ->method($method);
        return array(
            $viewMock,
            $viewMock,
        );
    }

    /**
     * Return view collection mock
     *
     * @return \Magento\Mview\View\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getViewsMock()
    {
        $viewsMock = $this->getMock('Magento\Mview\View\Collection', array(), array(), '', false);
        $this->viewsFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($viewsMock));
        return $viewsMock;
    }

    public function testUpdate()
    {
        $viewsMock = $this->getViewsMock();
        $viewsMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($this->getViews('update')));
        $viewsMock->expects($this->never())
            ->method('getItemsByColumnValue');

        $this->model->update();
    }

    public function testUpdateWithGroup()
    {
        $group = 'group';
        $viewsMock = $this->getViewsMock();
        $viewsMock->expects($this->never())
            ->method('getItems');
        $viewsMock->expects($this->once())
            ->method('getItemsByColumnValue')
            ->with($group)
            ->will($this->returnValue($this->getViews('update')));

        $this->model->update($group);
    }

    public function testClearChangelog()
    {
        $viewsMock = $this->getViewsMock();
        $viewsMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($this->getViews('clearChangelog')));
        $viewsMock->expects($this->never())
            ->method('getItemsByColumnValue');

        $this->model->clearChangelog();
    }

    public function testClearChangelogWithGroup()
    {
        $group = 'group';
        $viewsMock = $this->getViewsMock();
        $viewsMock->expects($this->never())
            ->method('getItems');
        $viewsMock->expects($this->once())
            ->method('getItemsByColumnValue')
            ->with($group)
            ->will($this->returnValue($this->getViews('clearChangelog')));

        $this->model->clearChangelog($group);
    }
}
