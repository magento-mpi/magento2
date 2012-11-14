<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Webapi_Block_Adminhtml_User
 */
class Mage_Webapi_Block_Adminhtml_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Event_Manager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    /**
     * @var Mage_Webapi_Block_Adminhtml_User
     */
    protected $_block;

    protected function setUp()
    {
        $this->_eventManager = $this->getMockBuilder('Mage_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('dispatch'))
            ->getMock();

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $helper->getBlock('Mage_Webapi_Block_Adminhtml_User', array(
            'eventManager' => $this->_eventManager,
            'urlBuilder' => $this->getMockBuilder('Mage_Backend_Model_Url')->disableOriginalConstructor()->getMock()
        ));
    }

    /**
     * Test getCreateUrl method
     */
    public function testToHtml()
    {
        $this->_block->toHtml();
    }
}
