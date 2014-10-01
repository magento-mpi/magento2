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

    /**
     * @var \Magento\Store\Model\Store | \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMock(
            '\Magento\Framework\App\Request\Http',
            ['getBaseUrl', 'isSecure', 'getHttpHost'],
            [],
            '',
            false,
            false
        );
        $this->requestMock
            ->expects($this->atLeastOnce())
            ->method('getHttpHost')
            ->will($this->returnValue('init.host'));
        $this->storeMock = $this->getMockBuilder('\Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->getMockForAbstractClass('\Magento\Framework\StoreManagerInterface');
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->storeMock));
        $this->objectManager =  new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->validatorFactory = $this->getMockBuilder('Magento\Framework\ValidatorFactory')
            ->disableOriginalConstructor()
            ->getMock();

    }

    /**
     * @param $path
     * @param $baseUrl
     * @dataProvider setCookiePathNonDefaultDataProvider
     */
    public function testSetCookiePathNonDefault($path, $baseUrl)
    {
        $this->requestMock->expects($this->atLeastOnce())->method('getBaseUrl')->will($this->returnValue($path));
        $this->storeMock->expects($this->atLeastOnce())
            ->method('getBaseUrl')
            ->will($this->returnValue($baseUrl));

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

        $this->assertEquals($baseUrl . 'backend', $adminConfig->getCookiePath());
    }

    /**
     * @return array
     */
    public function setCookiePathNonDefaultDataProvider()
    {
        $pub_dir = \Magento\Framework\App\Filesystem::PUB_DIR;
        $static_dir = \Magento\Framework\App\Filesystem::STATIC_VIEW_DIR;
        return array(array('', '/'), array('/' . $pub_dir, '/' . $pub_dir . '/' . $static_dir . '/'));
    }

    /**
     * Test for setting session name for admin
     *
     */
    public function testSetSessionNameByConstructor()
    {
        $this->requestMock->expects($this->atLeastOnce())->method('getBaseUrl')->will($this->returnValue(''));
        $this->storeMock->expects($this->atLeastOnce())
            ->method('getBaseUrl')
            ->will($this->returnValue('/'));

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
