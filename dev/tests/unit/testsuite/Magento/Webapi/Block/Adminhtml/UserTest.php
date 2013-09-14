<?php
/**
 * Test class for \Magento\Webapi\Block\Adminhtml\User
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Block_Adminhtml_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Block\Adminhtml\User
     */
    protected $_block;

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_block = $helper->getObject('Magento\Webapi\Block\Adminhtml\User', array(
            // TODO: Remove injecting of 'urlBuilder' after MAGETWO-5038 complete
            'urlBuilder' => $this->getMockBuilder('Magento\Backend\Model\Url')
                ->disableOriginalConstructor()
                ->getMock(),
        ));
    }

    /**
     * Test _construct method.
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals('Magento_Webapi', '_blockGroup', $this->_block);
        $this->assertAttributeEquals('adminhtml_user', '_controller', $this->_block);
        $this->assertAttributeEquals('API Users', '_headerText', $this->_block);
        $this->_assertBlockHasButton(0, 'add', 'Add New API User');
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
