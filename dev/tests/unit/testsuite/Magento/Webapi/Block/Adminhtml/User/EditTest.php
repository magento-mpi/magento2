<?php
/**
 * Test class for Magento_Webapi_Block_Adminhtml_User_Edit
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Block_Adminhtml_User_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Controller_Request_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @var Magento_Webapi_Block_Adminhtml_User_Edit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_request = $this->getMockBuilder('Magento_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getParam'))
            ->getMock();

        $this->_request->expects($this->any())
            ->method('getParam')
            ->with('user_id')
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
        $this->_block = $helper->getObject('Magento_Webapi_Block_Adminhtml_User_Edit', array(
            // TODO: Remove injecting of 'urlBuilder' after MAGETWO-5038 complete
            'urlBuilder' => $this->getMockBuilder('Magento_Backend_Model_Url')
                ->disableOriginalConstructor()
                ->getMock(),
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
        $this->assertAttributeEquals('adminhtml_user', '_controller', $this->_block);
        $this->assertAttributeEquals('user_id', '_objectId', $this->_block);
        $this->_assertBlockHasButton(1, 'save', 'label', 'Save API User');
        $this->_assertBlockHasButton(1, 'save', 'id', 'save_button');
        $this->_assertBlockHasButton(0, 'delete', 'label', 'Delete API User');
    }

    /**
     * Test getHeaderText method.
     */
    public function testGetHeaderText()
    {
        $apiUser = new Magento_Object();
        $this->_block->setApiUser($apiUser);
        $this->assertEquals('New API User', $this->_block->getHeaderText());

        $apiUser->setId(1)->setApiKey('test-api');

        $this->_coreData->expects($this->once())
            ->method('escapeHtml')
            ->with($apiUser->getApiKey())
            ->will($this->returnArgument(0));


        $this->assertEquals("Edit API User 'test-api'", $this->_block->getHeaderText());
    }

    /**
     * Asserts that block has button with ID and attribute at level.
     *
     * @param int $level
     * @param string $buttonId
     * @param string $attributeName
     * @param string $attributeValue
     */
    protected function _assertBlockHasButton($level, $buttonId, $attributeName, $attributeValue)
    {
        $buttonsProperty = new ReflectionProperty($this->_block, '_buttons');
        $buttonsProperty->setAccessible(true);
        $buttons = $buttonsProperty->getValue($this->_block);
        $this->assertInternalType('array', $buttons, 'Cannot get block buttons.');
        $this->assertArrayHasKey($level, $buttons, "Block doesn't have buttons at level $level");
        $this->assertArrayHasKey($buttonId, $buttons[$level], "Block doesn't have '$buttonId' button at level $level");
        $this->assertArrayHasKey($attributeName, $buttons[$level][$buttonId],
            "Block button doesn't have attribute $attributeName");
        $this->assertEquals($attributeValue, $buttons[$level][$buttonId][$attributeName],
            "Block button $attributeName' has unexpected value.");
    }
}
