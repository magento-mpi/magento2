<?php
/**
 * Test JSON Renderer for REST.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response_Rest_Renderer_JsonTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Response\Rest\Renderer\Json */
    protected $_restJsonRenderer;

    /** @var \Magento\Core\Helper\Data */
    protected $_helperMock;

    protected function setUp()
    {
        /** Prepare mocks and objects for SUT constructor. */
        $this->_helperMock = $this->getMockBuilder('Magento\Core\Helper\Data')->disableOriginalConstructor()->getMock();
        $helperFactoryMock = $this->getMock('Magento\Core\Model\Factory\Helper');
        $helperFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_helperMock));
        /** Initialize SUT. */
        $this->_restJsonRenderer = new \Magento\Webapi\Controller\Response\Rest\Renderer\Json($helperFactoryMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_helperMock);
        unset($this->_restJsonRenderer);
        parent::tearDown();
    }

    /**
     * Test render method.
     */
    public function testRender()
    {
        $arrayToRender = array('key' => 'value');
        /** Assert that jsonEncode method in mocked helper will run once */
        $this->_helperMock->expects($this->once())->method('jsonEncode');
        $this->_restJsonRenderer->render($arrayToRender);
    }

    /**
     * Test GetMimeType method.
     */
    public function testGetMimeType()
    {
        $expectedMimeType = 'application/json';
        $this->assertEquals($expectedMimeType, $this->_restJsonRenderer->getMimeType(), 'Unexpected mime type.');
    }
}
