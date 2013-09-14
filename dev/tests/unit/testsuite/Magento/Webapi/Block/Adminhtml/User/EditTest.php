<?php
/**
 * Test class for \Magento\Webapi\Block\Adminhtml\User\Edit
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Block_Adminhtml_User_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Controller\Request\Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Webapi\Block\Adminhtml\User\Edit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_layout = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper', 'getChildBlock', 'getChildName'))
            ->getMock();

        $this->_request = $this->getMockBuilder('Magento\Core\Controller\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getParam'))
            ->getMock();

        $this->_request->expects($this->any())
            ->method('getParam')
            ->with('user_id')
            ->will($this->returnValue(1));

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_block = $helper->getObject('Magento\Webapi\Block\Adminhtml\User\Edit', array(
            // TODO: Remove injecting of 'urlBuilder' after MAGETWO-5038 complete
            'urlBuilder' => $this->getMockBuilder('Magento\Backend\Model\Url')
                ->disableOriginalConstructor()
                ->getMock(),
            'layout' => $this->_layout,
            'request' => $this->_request
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
        $apiUser = new \Magento\Object();
        $this->_block->setApiUser($apiUser);
        $this->assertEquals('New API User', $this->_block->getHeaderText());

        $apiUser->setId(1)->setApiKey('test-api');

        /** @var PHPUnit_Framework_MockObject_MockObject $coreHelper  */
        $coreHelper = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('escapeHtml'))
            ->getMock();
        $coreHelper->expects($this->once())
            ->method('escapeHtml')
            ->with($apiUser->getApiKey())
            ->will($this->returnArgument(0));
        $this->_layout->expects($this->once())
            ->method('helper')
            ->with('Magento\Core\Helper\Data')
            ->will($this->returnValue($coreHelper));

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
