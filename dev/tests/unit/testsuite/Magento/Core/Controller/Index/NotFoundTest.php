<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Controller\Index;

use Magento\TestFramework\Helper\ObjectManager;

class NotFoundTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        /**
         * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\App\Response\Http
         */
        $responseMock = $this->getMockBuilder('Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->once())->method('setHeader')->with('HTTP/1.1', '404 Not Found');
        $responseMock->expects($this->once())->method('setHttpResponseCode')->with(404);
        $responseMock->expects($this->once())->method('setBody')->with('Requested resource not found');

        $objectManager = new ObjectManager($this);

        /**
         * @var \Magento\Core\Controller\Index
         */
        $controller = $objectManager->getObject(
            'Magento\Core\Controller\Index\NotFound',
            ['response' => $responseMock]
        );

        // The execute method is empty and returns void, just calling to verify
        // the method exists and does not throw an exception
        $controller->execute();
    }
}
