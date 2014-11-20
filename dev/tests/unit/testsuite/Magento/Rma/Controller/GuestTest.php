<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller;

/**
 * Class GuestTest
 * @package Magento\Rma\Controller
 */
abstract class GuestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Guest
     */
    protected $controller;

    /**
     * @var \Magento\Framework\Registry | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\Request\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Response\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Framework\ObjectManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Url | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\Message\Manager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->response = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->messageManager = $this->getMock('Magento\Framework\Message\Manager', [], [], '', false);
        $this->redirect = $this->getMock('Magento\Store\App\Response\Redirect', [], [], '', false);
        $this->url = $this->getMock('Magento\Framework\Url', [], [], '', false);

        $context = $this->getMock('Magento\Framework\App\Action\Context', [], [], '', false);
        $context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
        $context->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $context->expects($this->once())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManager));
        $context->expects($this->once())
            ->method('getRedirect')
            ->will($this->returnValue($this->redirect));
        $context->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($this->url));

        $this->coreRegistry = $this->getMock('Magento\Framework\Registry', ['registry'], [], '', false);

        $this->controller = $objectManagerHelper->getObject(
            '\\Magento\\Rma\\Controller\\Guest\\' . $this->name,
            [
                'coreRegistry' => $this->coreRegistry,
                'context' => $context
            ]
        );
    }
}
