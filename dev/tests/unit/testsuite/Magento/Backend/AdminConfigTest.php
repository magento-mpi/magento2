<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend;

use Magento\TestFramework\ObjectManager;

/**
 * Test class for \Magento\Backend\AdminConfig
 *
 */
class AdminConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $requestMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $objectManager;


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
    }

    public function testSetCookiePathNonDefault()
    {
        $mockFrontNameResolver = $this->getMockBuilder('\Magento\Backend\App\Area\FrontNameResolver')
            ->disableOriginalConstructor()
            ->getMock();

        $mockFrontNameResolver->expects($this->once())
            ->method('getFrontName')
            ->will($this->returnValue('backend'));

        $adminConfig = $this->objectManager->getObject(
            'Magento\Backend\AdminConfig',
            [
                'request' => $this->requestMock,
                'frontNameResolver' => $mockFrontNameResolver,
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
        $adminConfig = $this->objectManager->getObject(
            'Magento\Backend\AdminConfig',
            [
                'request' => $this->requestMock,
                'sessionName' => $sessionName
            ]
        );
        $this->assertSame($sessionName, $adminConfig->getName());
    }
}
