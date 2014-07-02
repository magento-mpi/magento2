<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Controller;

class IpnTest extends \PHPUnit_Framework_TestCase
{
    /** @var Ipn */
    protected $model;

    /** @var \Magento\Framework\Logger|\PHPUnit_Framework_MockObject_MockObject */
    protected $logger;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $response;

    protected function setUp()
    {
        $this->logger = $this->getMock('Magento\Framework\Logger', [], [], '', false);
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->response = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Paypal\Controller\Ipn',
            [
                'logger' => $this->logger,
                'request' => $this->request,
                'response' => $this->response,
            ]
        );
    }

    public function testIndexActionException()
    {
        $this->request->expects($this->once())->method('isPost')->will($this->returnValue(true));
        $exception = new \Exception();
        $this->request->expects($this->once())->method('getPost')->will($this->throwException($exception));
        $this->logger->expects($this->once())->method('logException')->with($this->identicalTo($exception));
        $this->response->expects($this->once())->method('setHttpResponseCode')->with(500);
        $this->model->indexAction();
    }
}
