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
        $this->assertAttributeEquals('Add New API User', '_addButtonLabel', $this->_block);
        $this->assertAttributeEquals('Back', '_backButtonLabel', $this->_block);
    }
}
