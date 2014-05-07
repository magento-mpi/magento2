<?php
/**
 * RouterList model test class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class RouterListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RouterList
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var array
     */
    protected $routerList;

    protected function setUp()
    {
        $this->routerList = array(
            'adminRouter' => array('class' => 'AdminClass', 'disable' => true, 'sortOrder' => 10),
            'frontendRouter' => array('class' => 'FrontClass', 'disable' => false, 'sortOrder' => 10),
            'default' => array('class' => 'DefaultClass', 'disable' => false, 'sortOrder' => 5)
        );

        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');
        $this->model = new \Magento\Framework\App\RouterList($this->objectManagerMock, $this->routerList);
    }

    public function testCurrent()
    {
        $expectedClass = new DefaultClass();
        $this->objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('DefaultClass')
            ->will($this->returnValue($expectedClass));

        $this->assertEquals($expectedClass, $this->model->current());
    }

    public function testNext()
    {
        $expectedClass = new FrontClass();
        $this->objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('FrontClass')
            ->will($this->returnValue($expectedClass));

        $this->model->next();
        $this->assertEquals($expectedClass, $this->model->current());
    }

    public function testValid()
    {
        $this->assertTrue($this->model->valid());
        $this->model->next();
        $this->assertTrue($this->model->valid());
        $this->model->next();
        $this->assertFalse($this->model->valid());
    }

    public function testRewind()
    {
        $frontClass = new FrontClass();
        $defaultClass = new DefaultClass();

        $this->objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('DefaultClass')
            ->will($this->returnValue($defaultClass));

        $this->objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with('FrontClass')
            ->will($this->returnValue($frontClass));

        $this->assertEquals($defaultClass, $this->model->current());
        $this->model->next();
        $this->assertEquals($frontClass, $this->model->current());
        $this->model->rewind();
        $this->assertEquals($defaultClass, $this->model->current());
    }

}
