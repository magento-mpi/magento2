<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\File\FileList;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    const CREATE_EXCEPTION_MESSAGE =
        'Magento\Framework\View\Layout\File\FileList\Collator has to implement the collate interface.';

    /**
     * @var \Magento\Framework\View\Layout\File\FileList\Factory
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = $this->getMockForAbstractClass('Magento\Framework\ObjectManager');
        $this->model = new \Magento\Framework\View\Layout\File\FileList\Factory($this->objectManager);
    }

    public function testCreate()
    {
        $helperObjectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $collator = $helperObjectManager->getObject(
            \Magento\Framework\View\Layout\File\FileList\Factory::FILE_LIST_COLLATOR
        );
        $list = $helperObjectManager->getObject('Magento\Framework\View\Layout\File\FileList');

        $this->objectManager->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $this->equalTo(\Magento\Framework\View\Layout\File\FileList\Factory::FILE_LIST_COLLATOR)
        )->will(
            $this->returnValue($collator)
        );

        $this->objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $this->equalTo('Magento\Framework\View\Layout\File\FileList'),
            $this->equalTo(array('collator' => $collator))
        )->will(
            $this->returnValue($list)
        );
        $this->assertSame($list, $this->model->create());
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage \Magento\Framework\View\Layout\File\FileList\FactoryTest::CREATE_EXCEPTION_MESSAGE
     */
    public function testCreateException()
    {
        $collator = new \stdClass();

        $this->objectManager->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $this->equalTo(\Magento\Framework\View\Layout\File\FileList\Factory::FILE_LIST_COLLATOR)
        )->will(
            $this->returnValue($collator)
        );

        $this->model->create();
    }
}
