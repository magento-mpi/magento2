<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Import_Edit_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configurationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_importModelMock;

    /**
     * @var Saas_ImportExport_Block_Adminhtml_Import_Edit_Image
     */
    protected $_block;

    public function setUp()
    {
        $this->_urlBuilderMock = $this->getMock('Mage_Core_Model_UrlInterface');
        $contextMock = $this->getMock('Mage_Backend_Block_Template_Context', array(), array(), '', false);
        $contextMock->expects($this->once())->method('getUrlBuilder')->will($this->returnValue($this->_urlBuilderMock));

        $this->_configurationMock = $this->getMock('Saas_ImportExport_Helper_Import_Image_Configuration', array(),
            array(), '', false);
        $this->_coreHelperMock = $this->getMock('Mage_Core_Helper_Data', array(), array(), '', false);
        $this->_importModelMock = $this->getMock('Mage_ImportExport_Model_Import', array(), array(), '', false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManager->getObject('Saas_ImportExport_Block_Adminhtml_Import_Edit_Image', array(
            'context' => $contextMock,
            'configuration' => $this->_configurationMock,
            'coreHelper' => $this->_coreHelperMock,
            'importModel' => $this->_importModelMock,
        ));
    }

    public function testGetTypeCode()
    {
        $value = 'type-code';
        $this->_configurationMock->expects($this->once())->method('getTypeCode')->will($this->returnValue($value));

        $this->assertEquals($value, $this->_block->getTypeCode());
    }

    public function testGetUniqueBehaviorsAsJson()
    {
        $value = 'json encoded';
        $class = $this->_importModelMock;
        $class::staticExpects($this->once())->method('getUniqueEntityBehaviors')
            ->will($this->returnValue(array('key1' => 'test1', 'key2' => 'test2', 'key3' => 'test3')));

        $this->_coreHelperMock->expects($this->once())->method('jsonEncode')->with(array('key1', 'key2', 'key3'))
            ->will($this->returnValue($value));

        $this->assertEquals($value, $this->_block->getUniqueBehaviorsAsJson());
    }

    public function testGetFormAction()
    {
        $value = 'form action';
        $this->_urlBuilderMock->expects($this->once())->method('getUrl')->with('*/import_images/import', array())
            ->will($this->returnValue($value));

        $this->assertEquals($value, $this->_block->getFormAction());
    }
}
