<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileSizeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheTypeListMock;

    /**
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_fileSizeMock = $this->getMock('Magento_File_Size', array(), array(), '', false);
        $this->_cacheTypeListMock = $this->getMock('Mage_Core_Model_Cache_TypeListInterface');

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_helper = $objectManager->getObject('Saas_ImportExport_Helper_Data', array(
            'fileSize' => $this->_fileSizeMock,
            'cacheTypeList' => $this->_cacheTypeListMock,
        ));
    }

    public function testGetMaxFileSizeInMb()
    {
        $value = 1235;
        $this->_fileSizeMock->expects($this->once())->method('getMaxFileSizeInMb')->will($this->returnValue($value));

        $this->assertEquals($value, $this->_helper->getMaxFileSizeInMb());
    }

    public function testCleanPageCache()
    {
        $this->_cacheTypeListMock->expects($this->at(0))->method('invalidate')
            ->with(Enterprise_PageCache_Model_Cache_Type::TYPE_IDENTIFIER);
        $this->_cacheTypeListMock->expects($this->at(1))->method('invalidate')
            ->with(Mage_Core_Model_Cache_Type_Block::TYPE_IDENTIFIER);

        $this->_helper->cleanPageCache();
    }
}
