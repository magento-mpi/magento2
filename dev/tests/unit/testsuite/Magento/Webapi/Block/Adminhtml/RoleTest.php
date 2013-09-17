<?php
/**
 * Test class for Magento_Webapi_Block_Adminhtml_Role
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Block_Adminhtml_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var Magento_Webapi_Block_Adminhtml_Role
     */
    protected $_block;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMockBuilder('Magento_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_block = $helper->getObject('Magento_Webapi_Block_Adminhtml_Role', array(
            'urlBuilder' => $this->_urlBuilder
        ));
    }

    /**
     * Test _construct method.
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals('Magento_Webapi', '_blockGroup', $this->_block);
        $this->assertAttributeEquals('adminhtml_role', '_controller', $this->_block);
        $this->assertAttributeEquals('API Roles', '_headerText', $this->_block);
        $this->_assertBlockHasButton(0, 'add', 'Add New API Role');
    }

    /**
     * Test getCreateUrl method.
     */
    public function testGetCreateUrl()
    {
        $expectedUrl = 'create_url';
        $this->_urlBuilder
            ->expects($this->once())
            ->method('getUrl')
            ->with('*/*/edit', array())
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_block->getCreateUrl());
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
