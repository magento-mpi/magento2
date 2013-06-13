<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Export_ResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_ImportExport_Block_Adminhtml_Export_Result
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stateHelperMock;

    public function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);

        $this->_stateHelperMock = $this->getMock('Saas_ImportExport_Helper_Export_State', array(), array(), '', false);
        $this->_block = $objectManager->getObject('Saas_ImportExport_Block_Adminhtml_Export_Result', array(
            'context' => $this->getMock('Mage_Backend_Block_Template_Context', array(), array(), '', false),
            'stateHelper' => $this->_stateHelperMock,
            'fileHelper' => $this->getMock('Saas_ImportExport_Helper_Export_File', array(), array(), '', false),
        ));
    }

    public function testIsExportInProgress()
    {
        $this->_stateHelperMock->expects($this->once())->method('isInProgress');
        $this->_block->isExportInProgress();
    }

    public function testGetCheckExportUrl()
    {
        $blockMock = $this->getMock('Saas_ImportExport_Block_Adminhtml_Export_Result', array('getUrl'),
            array(), '', false);
        $blockMock->expects($this->once())->method('getUrl')->with('*/*/check')->will($this->returnValue('some-url'));

        $this->assertEquals('some-url', $blockMock->getCheckExportUrl());
    }

    public function testGetCheckExportTimeout()
    {
        $this->assertEquals(Saas_ImportExport_Block_Adminhtml_Export_Result::TIMEOUT_CHECK_EXPORT_PROGRESS,
            $this->_block->getCheckExportTimeout());
    }
}
