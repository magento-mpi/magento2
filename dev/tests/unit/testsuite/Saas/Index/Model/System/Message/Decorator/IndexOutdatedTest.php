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
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag', array(), array(), '', false);
        $this->_flagMock->expects($this->once())->method('loadSelf')->will($this->returnSelf());
        $factoryMock = $this->getMock('Saas_Index_Model_FlagFactory', array('create'), array(), '', false);
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_flagMock));

        $this->_helperMock = $this->getMock('Saas_Index_Helper_Data', array(), array(), '', false);
        $this->_urlBuilder = $this->getMock('Mage_Core_Model_UrlInterface');
        $this->_messageMock = $this->getMock('Mage_Index_Model_System_Message_IndexOutdated', array(), array(), '',
            false);
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_message = $objectManager->getObject('Saas_Index_Model_System_Message_Decorator_IndexOutdated', array(
            'message' => $this->_messageMock,
            'helper' => $this->_helperMock,
            'urlBuilder' => $this->_urlBuilder,
            'flagFactory' => $factoryMock,
        ));
    }

    /**
     * @param bool $isShow
     * @param bool $isDisplayed
     * @param bool $result
     * @dataProvider dataProviderForIsDisplayed
     */
    public function testIsDisplayed($isShow, $isDisplayed, $result)
    {
        $this->_flagMock->expects($this->once())->method('isShowIndexNotification')->will($this->returnValue($isShow));
        $this->_messageMock->expects($this->any())->method('isDisplayed')->will($this->returnValue($isDisplayed));

        $this->assertEquals($result, $this->_message->isDisplayed());
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

    /**
     * @param string $method
     * @dataProvider dataProviderDecorationMethods
     */
    public function testDecorationMethods($method)
    {
        $this->_messageMock->expects($this->once())->method($method)->will($this->returnValue('some result'));

        $this->assertEquals('some result', $this->_message->$method());
    }

    /**
     * @return array
     */
    public function dataProviderDecorationMethods()
    {
        return array(
            array('getIdentity'),
            array('getSeverity'),
        );
    }

    public function testGetText()
    {
        $url = 'some-url';
        $translatedMessage = 'translated-message';

        $this->_urlBuilder->expects($this->once())->method('getUrl')->with('adminhtml/process/list')
            ->will($this->returnValue($url));

        $this->_helperMock->expects($this->once())->method('__')
            ->with('You need to refresh the search index. Please click <a href="%s">here</a>.', $url)
            ->will($this->returnValue($translatedMessage));

        $this->assertEquals($translatedMessage, $this->_message->getText());
    }
}
