<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\FileList;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Layout\File\FileList\Factory
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $this->_model = new \Magento\View\Layout\File\FileList\Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $helperObjectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $list = $helperObjectManager->getObject('Magento\View\Layout\File\FileList');

        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Magento\View\Layout\File\FileList')
            ->will($this->returnValue($list))
        ;
        $this->assertSame($list, $this->_model->create());
    }
}
