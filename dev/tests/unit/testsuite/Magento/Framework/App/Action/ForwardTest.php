<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Action;

class ForwardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Action\Forward
     */
    protected $_actionAbstract;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    protected function setUp()
    {
        $this->_request = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $this->_response = $this->getMock('\Magento\Framework\App\Response\Http', array(), array(), '', false);

        $this->_actionAbstract = new \Magento\Framework\App\Action\Forward($this->_request, $this->_response);
    }

    public function testDispatch()
    {
        $this->_request->expects($this->once())->method('setDispatched')->with(false);
        $this->_actionAbstract->dispatch($this->_request);
    }

    /**
     * Test for getRequest method
     *
     * @test
     * @covers \Magento\Framework\App\Action\AbstractAction::getRequest
     */
    public function testGetRequest()
    {
        $this->assertEquals($this->_request, $this->_actionAbstract->getRequest());
    }

    /**
     * Test for getResponse method
     *
     * @test
     * @covers \Magento\Framework\App\Action\AbstractAction::getResponse
     */
    public function testGetResponse()
    {
        $this->assertEquals($this->_response, $this->_actionAbstract->getResponse());
    }

    /**
     * Test for getResponse med. Checks that response headers are set correctly
     *
     * @test
     * @covers \Magento\Framework\App\Action\AbstractAction::getResponse
     */
    public function testResponseHeaders()
    {
        $infoProcessorMock = $this->getMock('Magento\Framework\App\Request\PathInfoProcessorInterface');
        $routerListMock = $this->getMock('Magento\Framework\App\Route\ConfigInterface');
        $cookieMock = $this->getMock('Magento\Stdlib\Cookie', array(), array(), '', false);
        $contextMock = $this->getMock('Magento\Framework\App\Http\Context', array(), array(), '', false);
        $request = new \Magento\Framework\App\Request\Http($routerListMock, $infoProcessorMock);
        $response = new \Magento\Framework\App\Response\Http($cookieMock, $contextMock);
        $response->headersSentThrowsException = false;
        $action = new \Magento\Framework\App\Action\Forward($request, $response);

        $this->assertEquals(array(), $action->getResponse()->getHeaders());
    }
}
