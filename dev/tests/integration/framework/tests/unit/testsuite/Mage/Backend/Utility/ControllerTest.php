<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Utility_ControllerTest extends Mage_Backend_Utility_Controller
{
    protected function setUp()
    {
        // parent method is not called intentionally to avoid operations requiring fully initialized application
        $messagesCollection = new Mage_Core_Model_Message_Collection();
        $messagesCollection
            ->add(new Mage_Core_Model_Message_Warning('some_warning'))
            ->add(new Mage_Core_Model_Message_Error('error_one'))
            ->add(new Mage_Core_Model_Message_Error('error_two'))
            ->add(new Mage_Core_Model_Message_Notice('some_notice'))
        ;
        $this->_objectManager = $this->getMockForAbstractClass('Magento_ObjectManager');
        $this->_objectManager
            ->expects($this->once())
            ->method('get')
            ->with('Mage_Backend_Model_Session')
            ->will($this->returnValue(new Varien_Object(array('messages' => $messagesCollection))))
        ;
    }

    protected function tearDown()
    {
        // parent method is not called intentionally
        $this->_objectManager = null;
    }

    /**
     * @param array $expectedMessages
     * @param string|null $messageTypeFilter
     * @dataProvider assertAdminMessagesDataProvider
     */
    public function testAssertAdminMessagesSuccess(array $expectedMessages, $messageTypeFilter)
    {
        $constraint = $this->getMock('PHPUnit_Framework_Constraint', array('toString', 'matches'));
        $constraint
            ->expects($this->once())
            ->method('matches')
            ->with($expectedMessages)
            ->will($this->returnValue(true))
        ;
        $this->assertAdminMessages($constraint, $messageTypeFilter);
    }

    public function assertAdminMessagesDataProvider()
    {
        return array(
            'no message type filtering' => array(array('some_warning', 'error_one', 'error_two', 'some_notice'), null),
            'message type filtering'    => array(array('error_one', 'error_two'), Mage_Core_Model_Message::ERROR),
        );
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Admin panel messages do not meet expectations
     */
    public function testAssertAdminMessagesFailure()
    {
        $this->assertAdminMessages($this->isEmpty());
    }
}
