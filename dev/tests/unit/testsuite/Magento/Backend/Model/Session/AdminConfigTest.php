<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Backend\Model\Session\AdminConfig
 */
namespace Magento\Backend\Model\Session;

use Magento\TestFramework\ObjectManager;

class AdminConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \Magento\Framework\ValidatorFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $validatorFactory;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\StoreManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMock(
            '\Magento\Framework\App\Request\Http',
            ['getBasePath', 'isSecure', 'getHttpHost'],
            [],
            '',
            false,
            false
        );
        $this->requestMock->expects($this->atLeastOnce())->method('getBasePath')->will($this->returnValue('/'));
        $this->requestMock->expects(
            $this->atLeastOnce()
        )->method(
            'getHttpHost'
        )->will(
            $this->returnValue('init.host')
        );
        $this->objectManager =  new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->validatorFactory = $this->getMockBuilder('Magento\Framework\ValidatorFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $storeMock = $this->getMockBuilder('\Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())->method('getBaseUrl')->will($this->returnValue('/'));
        $this->storeManagerMock = $this->getMockForAbstractClass('\Magento\Framework\StoreManagerInterface');
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

    }

    public function testSetCookiePathNonDefault()
    {
        $mockFrontNameResolver = $this->getMockBuilder('\Magento\Backend\App\Area\FrontNameResolver')
            ->disableOriginalConstructor()
            ->getMock();

        $mockFrontNameResolver->expects($this->once())
            ->method('getFrontName')
            ->will($this->returnValue('backend'));

        $validatorMock = $this->getMockBuilder('Magento\Framework\Validator\ValidatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $validatorMock->expects($this->any())
            ->method('isValid')
            ->willReturn(true);
        $this->validatorFactory->expects($this->any())
            ->method('setInstanceName')
            ->willReturnSelf();
        $this->validatorFactory->expects($this->any())
            ->method('create')
            ->willReturn($validatorMock);
        $adminConfig = $this->objectManager->getObject(
            'Magento\Backend\Model\Session\AdminConfig',
            [
                'validatorFactory' => $this->validatorFactory,
                'request' => $this->requestMock,
                'frontNameResolver' => $mockFrontNameResolver,
                'storeManager' => $this->storeManagerMock
            ]
        );

        $this->assertEquals('/backend', $adminConfig->getCookiePath());
    }

    /**
     * Test for setting session name for admin
     *
     */
    public function testSetSessionNameByConstructor()
    {
        $sessionName = 'admin';

        $validatorMock = $this->getMockBuilder('Magento\Framework\Validator\ValidatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $validatorMock->expects($this->any())
            ->method('isValid')
            ->willReturn(true);
        $this->validatorFactory->expects($this->any())
            ->method('setInstanceName')
            ->willReturnSelf();
        $this->validatorFactory->expects($this->any())
            ->method('create')
            ->willReturn($validatorMock);

        $adminConfig = $this->objectManager->getObject(
            'Magento\Backend\Model\Session\AdminConfig',
            [
                'validatorFactory' => $this->validatorFactory,
                'request' => $this->requestMock,
                'sessionName' => $sessionName,
                'storeManager' => $this->storeManagerMock
            ]
        );
        $this->assertSame($sessionName, $adminConfig->getName());
    }
}