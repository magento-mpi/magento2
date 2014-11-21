<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Tax;

use Magento\TestFramework\Helper\ObjectManager;

class IgnoreTaxNotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $objectManager = new ObjectManager($this);
        $cacheTypeList = $this->getMockBuilder('\Magento\Framework\App\Cache\TypeList')
            ->disableOriginalConstructor()
            ->setMethods(['cleanType'])
            ->getMock();

        $request = $this->getMockBuilder('\Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(['getParam'])
            ->getMock();

        $request->expects($this->once())
            ->method('getParam')
            ->will($this->returnValue('tax'));

        $response = $this->getMockBuilder('\Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->setMethods(['setRedirect'])
            ->getMock();

        $config = $this->getMockBuilder('\Magento\Core\Model\Resource\Config')
            ->disableOriginalConstructor()
            ->setMethods(['saveConfig'])
            ->getMock();

        $manager = $this->getMockBuilder('\Magento\Framework\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(['get', 'create', 'configure'])
            ->getMock();
        $manager->expects($this->any())
            ->method('get')
            ->will($this->returnValue($config));

        $notification = $objectManager->getObject(
            'Magento\Tax\Controller\Adminhtml\Tax\IgnoreTaxNotification',
            [
                'objectManager' => $manager,
                'cacheTypeList' => $cacheTypeList,
                'request' => $request,
                'response' => $response
            ]
        );

        // No exception thrown
        $notification->execute();
    }
}
