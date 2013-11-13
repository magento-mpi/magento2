<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Action;

class ForwardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Action\Forward
     */
    protected $_model;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    protected function setUp()
    {
        $this->_request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false );
        $this->_response = $this->getMock('\Magento\App\Response\Http');
        $this->_model = new \Magento\App\Action\Forward($this->_request, $this->_response);
    }

    public function testDispatch()
    {
        $this->_request->expects($this->once())->method('setDispatched')->with(false);
        $this->_model->dispatch($this->_request);
    }
}