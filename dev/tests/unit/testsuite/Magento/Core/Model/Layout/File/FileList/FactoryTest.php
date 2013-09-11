<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_File_FileList_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\File\FileList\Factory
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('\Magento\ObjectManager');
        $this->_model = new \Magento\Core\Model\Layout\File\FileList\Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $list = new \Magento\Core\Model\Layout\File\ListFile();
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Magento\Core\Model\Layout\File\ListFile')
            ->will($this->returnValue($list))
        ;
        $this->assertSame($list, $this->_model->create());
    }
}
