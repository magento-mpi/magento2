<?php
/**
 * Test class for Magento_Webapi_Block_Adminhtml_Role_Edit
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Block_Adminhtml_Role_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Controller_Request_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var Magento_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @var Magento_Webapi_Block_Adminhtml_Role_Edit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMockBuilder('Magento_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_request = $this->getMockBuilder('Magento_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getParam'))
            ->getMock();

        $this->_request->expects($this->any())
            ->method('getParam')
            ->with('role_id')
            ->will($this->returnValue(1));

        $this->_coreData = $this->getMockBuilder('Magento_Core_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('escapeHtml'))
            ->getMock();

        $helperFactory = $this->getMockBuilder('Magento_Core_Model_Factory_Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $helperFactory->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Magento_Core_Helper_Data'))
            ->will($this->returnValue($this->_coreData));

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $helper->getObject('Magento_Webapi_Block_Adminhtml_Role_Edit', array(
            'urlBuilder' => $this->_urlBuilder,
            'request' => $this->_request,
            'helperFactory' => $helperFactory,
        ));
    }

    /**
     * Test _construct method.
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals('Magento_Webapi', '_blockGroup', $this->_block);
        $this->assertAttributeEquals('adminhtml_role', '_controller', $this->_block);
        $this->assertAttributeEquals('role_id', '_objectId', $this->_block);
        $this->_assertBlockHasButton(1, 'save', 'Save API Role');
        $this->_assertBlockHasButton(0, 'delete', 'Delete API Role');
    }

    /**
     * Test getSaveAndContinueUrl method.
     */
    public function testGetSaveAndContinueUrl()
    {
        $expectedUrl = 'save_and_continue_url';
        $this->_urlBuilder
            ->expects($this->once())
            ->method('getUrl')
            ->with('*/*/save', array('_current' => true, 'continue' => true))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_block->getSaveAndContinueUrl());
    }

    /**
     * Test getHeaderText method.
     */
    public function testGetHeaderText()
    {
        $apiRole = new Magento_Object();
        $this->_block->setApiRole($apiRole);
        $this->assertEquals('New API Role', $this->_block->getHeaderText());

        $apiRole->setId(1)->setRoleName('Test Role');

        $this->_coreData->expects($this->once())
            ->method('escapeHtml')
            ->with($apiRole->getRoleName())
            ->will($this->returnArgument(0));

        $this->assertEquals("Edit API Role 'Test Role'", $this->_block->getHeaderText());
    }

    /**
     * Asserts that block has button with ID and label at level.
     *
     * @param int $level
     * @param string $buttonId
     * @param string $label
     */
    protected function _assertBlockHasButton($level, $buttonId, $label)
    {
        $buttonsProperty = new ReflectionProperty($this->_block, '_buttons');
        $buttonsProperty->setAccessible(true);
        $buttons = $buttonsProperty->getValue($this->_block);
        $this->assertInternalType('array', $buttons, 'Cannot get block buttons.');
        $this->assertArrayHasKey($level, $buttons, "Block doesn't have buttons at level $level");
        $this->assertArrayHasKey($buttonId, $buttons[$level], "Block doesn't have '$buttonId' button at level $level");
        $this->assertArrayHasKey('label', $buttons[$level][$buttonId], "Block button doesn't have label.");
        $this->assertEquals($label, $buttons[$level][$buttonId]['label'], "Block button label has unexpected value.");
    }
}
