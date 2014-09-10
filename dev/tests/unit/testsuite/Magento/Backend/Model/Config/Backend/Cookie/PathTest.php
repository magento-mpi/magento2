<?php
/**
 * Unit test for Magento\Backend\Model\Config\Backend\Cookie\Path
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend\Cookie;

use \Magento\TestFramework\Helper\ObjectManager;
use \Magento\Framework\Session\Config\Validator\CookiePathValidator;

class PathTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject | CookiePathValidator */
    private $validatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Install\Model\Resource\Resource */
    private $resourceMock;

    /** @var \Magento\Backend\Model\Config\Backend\Cookie\Path */
    private $model;

    public function setUp()
    {
        $this->validatorMock = $this->getMockBuilder('Magento\Framework\Session\Config\Validator\CookiePathValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceMock = $this->getMockBuilder('Magento\Install\Model\Resource\Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Backend\Model\Config\Backend\Cookie\Path',
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
     * @expectedExceptionMessage Invalid cookie path
     */
    public function testBeforeSaveException()
    {
        $invalidCookieLifetime = 'invalid path';
        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($invalidCookieLifetime)
            ->willReturn(false);

        // Must throw exception
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
        $this->resourceMock->expects($this->any())->method('addCommitCallback')->willReturnSelf();

        // Must not throw exception
        $this->model->setValue($validCookieLifetime)->save();
    }
} 