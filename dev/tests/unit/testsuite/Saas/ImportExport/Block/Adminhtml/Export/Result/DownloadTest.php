<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Export_Result_DownloadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_ImportExport_Block_Adminhtml_Export_Result_Download
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileHelperMock;

    public function setUp()
    {
        $this->_fileHelperMock = $this->getMock('Saas_ImportExport_Helper_Export_File', array(), array(), '', false);
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManager->getObject('Saas_ImportExport_Block_Adminhtml_Export_Result_Download', array(
            'fileHelper' => $this->_fileHelperMock,
        ));
    }

    /**
     * Get export file name
     *
     * @return string
     */
    public function testGetFileNameSuccess()
    {
        $this->_fileHelperMock->expects($this->once())->method('isExist')->will($this->returnValue(true));
        $this->_fileHelperMock->expects($this->once())->method('getDownloadName')
            ->will($this->returnValue('some-name'));
        $this->assertEquals('some-name', $this->_block->getFileName());
    }

    /**
     * Get export file name
     *
     * @return string
     */
    public function testGetFileNameFail()
    {
        $this->_fileHelperMock->expects($this->once())->method('isExist')->will($this->returnValue(false));
        $this->_fileHelperMock->expects($this->never())->method('getDownloadName')->will($this->returnValue(''));
        $this->assertEquals('', $this->_block->getFileName());
    }

    /**
     * Is export file exist
     *
     * @return bool
     */
    public function testIsFileExist()
    {
        $this->_fileHelperMock->expects($this->once())->method('isExist')->will($this->returnValue(true));
        $this->assertTrue($this->_block->isFileExist());
    }

    /**
     * @return array
     */
    public function dataProviderUrlMethods()
    {
        return array(
            array('*/*/download', 'getDownloadUrl'),
            array('*/*/remove', 'getRemoveUrl'),
        );
    }

    /**
     * @param string $route
     * @param string $method
     * @dataProvider dataProviderUrlMethods
     */
    public function testGetUrlMethods($route, $method)
    {
        $blockMock = $this->getMock('Saas_ImportExport_Block_Adminhtml_Export_Result_Download',
            array('getUrl'), array(), '', false);
        $blockMock->expects($this->once())->method('getUrl')->with($route)
            ->will($this->returnValue('some-url'));

        $this->assertEquals('some-url', $blockMock->$method());
    }
}
