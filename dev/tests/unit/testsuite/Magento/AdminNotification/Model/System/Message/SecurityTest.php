<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminNotification_Model_System_Message_SecurityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_curlFactoryMock;

    /**
     * @var Magento_AdminNotification_Model_System_Message_Security
     */
    protected $_messageModel;

    public function setUp()
    {
        //Prepare objects for constructor
        $this->_cacheMock = $this->getMock('Magento_Core_Model_CacheInterface');
        $this->_storeConfigMock = $this->getMock('Magento_Core_Model_Store_Config',
            array('getConfig'), array(), '', false);
        $this->_curlFactoryMock = $this->getMock('Magento_HTTP_Adapter_CurlFactory',
            array('create'), array(), '', false);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = array(
            'cache' => $this->_cacheMock,
            'storeConfig' => $this->_storeConfigMock,
            'curlFactory' => $this->_curlFactoryMock,
        );
        $this->_messageModel = $objectManagerHelper->getObject(
            'Magento_AdminNotification_Model_System_Message_Security',
            $arguments);
    }

    /**
     *
     * @param $expectedResult
     * @param $cached
     * @param $response
     * @return void
     * @dataProvider isDisplayedDataProvider
     */
    public function testIsDisplayed($expectedResult, $cached, $response)
    {
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue($cached));
        $this->_cacheMock->expects($this->any())->method('save')->will($this->returnValue(null));

        $httpAdapterMock = $this->getMock('Magento_HTTP_Adapter_Curl', array(), array(), '', false);
        $httpAdapterMock->expects($this->any())->method('read')->will($this->returnValue($response));
        $this->_curlFactoryMock->expects($this->any())->method('create')->will($this->returnValue($httpAdapterMock));

        $this->_storeConfigMock->expects($this->any())->method('getConfig')->will($this->returnValue(null));

        $this->assertEquals($expectedResult, $this->_messageModel->isDisplayed());
    }

    public function isDisplayedDataProvider()
    {
        return array(
            'cached_case' => array(false, true, ''),
            'accessible_file' => array(true, false, 'HTTP/1.1 200'),
            'inaccessible_file' => array(false, false, 'HTTP/1.1 403')
        );
    }

    public function testGetText()
    {
        $messageStart = 'Your web server is configured incorrectly.';

        $this->assertStringStartsWith($messageStart, (string)$this->_messageModel->getText());
    }
}
