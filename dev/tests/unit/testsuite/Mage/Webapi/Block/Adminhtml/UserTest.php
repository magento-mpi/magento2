<?php
/**
 * Test class for Mage_Webapi_Block_Adminhtml_User
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Block_Adminhtml_User
     */
    protected $_block;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $helper->getBlock('Mage_Webapi_Block_Adminhtml_User', array(
            // TODO Remove injecting of 'urlBuilder' after MAGETWO-5038 complete
            'urlBuilder' => $this->getMockBuilder('Mage_Backend_Model_Url')
                ->disableOriginalConstructor()
                ->getMock(),
        ));
    }

    /**
     * Test _construct method
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals('Mage_Webapi', '_blockGroup', $this->_block);
        $this->assertAttributeEquals('adminhtml_user', '_controller', $this->_block);
        $this->assertAttributeEquals('API Users', '_headerText', $this->_block);
        $this->_assertBlockHasButton(0, 'add', 'Add New API User');
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
