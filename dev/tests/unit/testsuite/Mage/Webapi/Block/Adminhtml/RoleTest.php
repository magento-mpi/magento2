<?php
/**
 * Test class for Mage_Webapi_Block_Adminhtml_Role
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var Mage_Webapi_Block_Adminhtml_Role
     */
    protected $_block;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMockBuilder('Mage_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $helper->getBlock('Mage_Webapi_Block_Adminhtml_Role', array(
            'urlBuilder' => $this->_urlBuilder
        ));
    }

    /**
     * Test _construct method
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals('Mage_Webapi', '_blockGroup', $this->_block);
        $this->assertAttributeEquals('adminhtml_role', '_controller', $this->_block);
        $this->assertAttributeEquals('API Roles', '_headerText', $this->_block);
        $this->_assertBlockHasButton(0, 'add', 'Add New API Role');
    }

    /**
     * Test getCreateUrl method
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
     * Asserts that block has button with id and label at level
     *
     * @param int $level
     * @param string $id
     * @param string $label
     */
    protected function _assertBlockHasButton($level, $id, $label)
    {
        $buttonsProperty = new ReflectionProperty($this->_block, '_buttons');
        $buttonsProperty->setAccessible(true);
        $buttons = $buttonsProperty->getValue($this->_block);
        $this->assertInternalType('array', $buttons, 'Cannot get bloc buttons');
        $this->assertArrayHasKey($level, $buttons, "Block doesn't have buttons at level $level");
        $this->assertArrayHasKey($id, $buttons[$level], "Block doesn't have '$id' button at level $level");
        $this->assertArrayHasKey('label', $buttons[$level][$id], "Block button doesn't have label");
        $this->assertEquals($label, $buttons[$level][$id]['label'], "Block button label has unexpected value");
    }
}
