<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Backend\AdminConfig
 */
namespace Magento\Backend;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

class AdminConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $requestMock;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;


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
        $this->appState = $this->getMock('\Magento\Framework\App\State', array(), array(), '', false);
    }

    public function testSetCookiePathNonDefault()
    {
        $mockFrontNameResolver = $this->getMockBuilder('\Magento\Backend\App\Area\FrontNameResolver')
            ->disableOriginalConstructor()
            ->getMock();

        $mockFrontNameResolver
            ->method('getFrontName')
            ->will($this->returnValue('backend'));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $adminConfig = $objectManager->getObject(
            'Magento\Backend\AdminConfig',
            [
                'request' => $this->requestMock,
                'appState' => $this->appState,
                'frontNameResolver' => $mockFrontNameResolver,
            ]
        );

        $this->assertEquals('/backend', $adminConfig->getCookiePath());
    }
}
