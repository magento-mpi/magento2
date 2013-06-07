<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Website_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Website_Observer
     */
    protected $_observer;

    /**
     * @var Varien_Event_Observer
     */
    protected $_eventObserver;

    /**
     * @var Saas_Limitation_Model_Website_Limitation|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_websiteLimitation;

    public function setUp()
    {
        $this->_eventObserver = new Varien_Event_Observer(array('event' => new Varien_Object()));

        $this->_websiteLimitation = $this->getMock('Saas_Limitation_Model_Website_Limitation', array(), array(), '',
            false);

        $this->_observer = new Saas_Limitation_Model_Website_Observer($this->_websiteLimitation);
    }

    /**
     * @param bool $isObjectNew
     * @param bool $isCreateRestricted
     * @dataProvider restrictEntityCreationNoExceptionDataProvider
     */
    public function testRestrictEntityCreationNoException($isObjectNew, $isCreateRestricted)
    {
        $website = $this->getMock('Mage_Core_Model_Website', array(), array(), '', false);
        $website->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue($isObjectNew));
        $this->_eventObserver->getData('event')->setData('website', $website);

        $this->_websiteLimitation->expects($this->any())
            ->method('isCreateRestricted')
            ->will($this->returnValue($isCreateRestricted));

        $this->_observer->restrictEntityCreation($this->_eventObserver);
    }

    /**
     * @return array
     */
    public static function restrictEntityCreationNoExceptionDataProvider()
    {
        return array(
            'new object, when limitation is not reached' => array(true, false),
            'existing object, when limitation is reached' => array(false, true),
        );
    }

    public function testRestrictEntityCreationException()
    {
        $website = $this->getMock('Mage_Core_Model_Website', array(), array(), '', false);
        $website->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue(true));
        $this->_eventObserver->getData('event')->setData('website', $website);

        $this->_websiteLimitation->expects($this->any())
            ->method('isCreateRestricted')
            ->will($this->returnValue(true));
        $this->_websiteLimitation->expects($this->any())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue('exception_message'));

        try {
            $this->_observer->restrictEntityCreation($this->_eventObserver);
        } catch (Exception $e) {
            /** @var Mage_Core_Exception $e */
            $this->assertInstanceOf('Mage_Core_Exception', $e);
            $this->assertEquals('exception_message', $e->getMessage());

            $subErrors = $e->getMessages();
            $this->assertCount(1, $subErrors);
            $this->assertEquals($subErrors, $e->getMessages(Mage_Core_Model_Message::ERROR));

            /** @var Mage_Core_Model_Message_Error $subError */
            $subError = current($subErrors);
            $this->assertInstanceOf('Mage_Core_Model_Message_Error', $subError);
            $this->assertEquals('exception_message', $subError->getText());
            return;
        }
        $this->fail('Exception must be thrown');
    }

    /**
     * @param string $blockClass
     * @param bool $isCreateRestricted
     * @param bool $expectedRemoval
     * @dataProvider removeCreationButtonDataProvider
     */
    public function removeCreationButton($blockClass, $isCreateRestricted, $expectedRemoval)
    {
        $block = $this->getMock($blockClass, array('removeButton'), array(), '', false);
        $this->_eventObserver->getData('event')->setData('block', $block);

        $this->_websiteLimitation->expects($this->any())
            ->method('isCreateRestriced')
            ->will($this->returnValue($isCreateRestricted));

        if ($expectedRemoval) {
            $block->expects($this->once())
                ->method('removeButton')
                ->with('add');
        } else {
            $block->expects($this->never())
                ->method('removeButton');
        }

        $this->_observer->removeCreationButton($this->_eventObserver);
    }

    public static function removeCreationButtonDataProvider()
    {
        return array(
            'not restricted - other block' => array(
                'Mage_Core_Block_Text',
                true,
                false,
            ),
            'not restricted - limitation is not reached' => array(
                'Mage_Adminhtml_Block_System_Store_Store',
                false,
                false,
            ),
            'restricted - right block and limitation is reached' => array(
                'Mage_Adminhtml_Block_System_Store_Store',
                true,
                true,
            ),
        );
    }
}
