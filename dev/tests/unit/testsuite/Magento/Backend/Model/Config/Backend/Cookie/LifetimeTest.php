<?php
/**
 * Unit test for Magento\Backend\Model\Config\Backend\Cookie\Lifetime
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend\Cookie;

use \Magento\TestFramework\Helper\ObjectManager;
use \Magento\Framework\Session\Config\Validator\CookieLifetimeValidator;

class LifetimeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject | CookieLifetimeValidator */
    private $validatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Module\Resource */
    private $resourceMock;

    /** @var \Magento\Backend\Model\Config\Backend\Cookie\Lifetime */
    private $model;

    public function setUp()
    {
        $this->validatorMock = $this->getMockBuilder(
            'Magento\Framework\Session\Config\Validator\CookieLifetimeValidator'
        )->disableOriginalConstructor()
            ->getMock();
        $this->resourceMock = $this->getMockBuilder('Magento\Framework\Module\Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Backend\Model\Config\Backend\Cookie\Lifetime',
            [
                'configValidator' => $this->validatorMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Invalid cookie lifetime: must be numeric
     */
    public function testBeforeSaveException()
    {
        $invalidCookieLifetime = 'invalid lifetime';
        $messages = ['must be numeric'];
        $this->validatorMock->expects($this->once())
            ->method('getMessages')
            ->willReturn($messages);
        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($invalidCookieLifetime)
            ->willReturn(false);

        // Test
        $this->model->setValue($invalidCookieLifetime)->save();
    }

    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     * No assertions exist because the purpose of the test is to make sure that no
     * exception gets thrown
     */
    public function testBeforeSaveNoException()
    {
        $validCookieLifetime = 1;
        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($validCookieLifetime)
            ->willReturn(true);
        $this->resourceMock->expects($this->once())->method('addCommitCallback')->willReturnSelf();

        // Test
        $this->model->setValue($validCookieLifetime)->save();
    }

    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     * No assertions exist because the purpose of the test is to make sure that no
     * exception gets thrown
     */
    public function testBeforeEmptyString()
    {
        $validCookieLifetime = '';
        $this->validatorMock->expects($this->never())
            ->method('isValid');

        $this->resourceMock->expects($this->once())->method('addCommitCallback')->willReturnSelf();

        // Test
        $this->model->setValue($validCookieLifetime)->save();
    }
}
