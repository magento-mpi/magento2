<?php
/**
 * Test class for \Magento\Webapi\Block\Adminhtml\User\Edit
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Block\Adminhtml\User;

class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Webapi\Block\Adminhtml\User\Edit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_request = $this->getMockBuilder('Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_request->expects($this->any())
            ->method('getParam')
            ->with('user_id')
            ->will($this->returnValue(1));

        $this->_escaper = $this->getMockBuilder('Magento\Escaper')
            ->disableOriginalConstructor()
            ->setMethods(array('escapeHtml'))
            ->getMock();

        $urlBuilder = $this->getMockBuilder('Magento\Backend\Model\Url')
            ->setMethods(array('getUrl'))
            ->disableOriginalConstructor()
            ->getMock();

        $context = $this->getMockBuilder('Magento\Backend\Block\Template\Context')
            ->disableOriginalConstructor()
            ->setMethods(array('getEscaper', 'getUrlBuilder', 'getRequest'))
            ->getMock();

        $context->expects($this->any())
            ->method('getEscaper')
            ->will($this->returnValue($this->_escaper));

        $context->expects($this->any())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        $context->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->_request));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $objectManager->getObject('Magento\Webapi\Block\Adminhtml\User\Edit', array(
            'context' => $context
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

        $this->_escaper->expects($this->once())
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
        $buttonsProperty = new \ReflectionProperty($this->_block, '_buttons');
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
