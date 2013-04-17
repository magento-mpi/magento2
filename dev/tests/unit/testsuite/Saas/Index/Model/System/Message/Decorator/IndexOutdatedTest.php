<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Model_System_Message_Decorator_IndexOutdatedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Index_Model_System_Message_Decorator_IndexOutdated
     */
    protected $_message;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_flagMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_messageMock;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $factoryMock = $this->getMock('Saas_Index_Model_FlagFactory', array('create'), array(), '', false);
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag',
            array('getState', 'setState', 'save', 'loadSelf', 'isShowIndexNotification'),
            array(), '', false
        );
        $this->_flagMock->expects($this->once())->method('loadSelf');
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_flagMock));

        $this->_helperMock = $this->getMock('Saas_Index_Helper_Data', array(), array(), '', false);
        $this->_urlBuilder = $this->getMock('Mage_Core_Model_UrlInterface');
        $this->_messageMock = $this->getMock('Mage_Index_Model_System_Message_IndexOutdated',
            array(), array(), '', false
        );
        $arguments = array(
            'message' => $this->_messageMock,
            'helper' => $this->_helperMock,
            'urlBuilder' => $this->_urlBuilder,
            'flagFactory' => $factoryMock,
        );
        $this->_message = $helper->getObject('Saas_Index_Model_System_Message_Decorator_IndexOutdated', $arguments);
    }

    /**
     * @param bool $state
     * @param bool $isDisplayed
     * @param bool $result
     * @dataProvider dataProviderForIsDisplayed
     */
    public function testIsDisplayed($state, $isDisplayed, $result)
    {
        $this->_flagMock->expects($this->once())->method('isShowIndexNotification')->will($this->returnValue($state));
        $this->_messageMock->expects($this->any())->method('isDisplayed')->will($this->returnValue($isDisplayed));
        $this->assertEquals($result, $this->_message->isDisplayed());
    }

    public function testGetIdentity()
    {
        $result = 'some string';
        $this->_messageMock->expects($this->once())->method('getIdentity')->will($this->returnValue($result));
        $this->assertEquals($result, $this->_message->getIdentity());
    }

    public function testGetSeverity()
    {
        $result = 'some string';
        $this->_messageMock->expects($this->once())->method('getSeverity')->will($this->returnValue($result));
        $this->assertEquals($result, $this->_message->getSeverity());
    }

    public function testGetText()
    {
        $url = 'some-url';
        $this->_urlBuilder->expects($this->once())->method('getUrl')->with('adminhtml/process/list')
            ->will($this->returnValue($url));
        $this->_helperMock->expects($this->once())->method('__')->will($this->returnArgument(0));
        $this->assertEquals(
            'You need to refresh the search index. Please click <a href="%s">here</a>.',
            $this->_message->getText()
        );
    }

    /**
     * @return array
     */
    public function dataProviderForIsDisplayed()
    {
        return array(
            array(true, true, true),
            array(true, false, false),
            array(false, true, false),
            array(false, false, false),
        );
    }
}
