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
     * @var Magento_Core_Model_Layout_File_FileList_Factory
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('\Magento\ObjectManager');
        $this->_model = new Magento_Core_Model_Layout_File_FileList_Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $list = new Magento_Core_Model_Layout_File_List();
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Magento_Core_Model_Layout_File_List')
            ->will($this->returnValue($list))
        ;
        $this->assertSame($list, $this->_model->create());
    }
}
