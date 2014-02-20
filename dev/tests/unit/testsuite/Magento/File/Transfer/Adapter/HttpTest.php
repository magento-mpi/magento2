<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\File\Transfer\Adapter;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend_Controller_Response_Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $response;

    /**
     * @var Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $object;

    protected function setUp()
    {
        $this->response = $this->getMock('Zend_Controller_Response_Http');
        $this->object = new Http($this->response, new \Magento\File\Mime);
    }

    public function testSend()
    {
        $file = __DIR__ . '/../../_files/javascript.js';

        $this->response->expects($this->at(0))
            ->method('setHeader')
            ->with('Content-length', filesize($file));
        $this->response->expects($this->at(1))
            ->method('setHeader')
            ->with('Content-Type', 'application/javascript');
        $this->response->expects($this->once())
            ->method('sendHeaders');
        $this->expectOutputString(file_get_contents($file));

        $this->object->send($file);
    }
}
