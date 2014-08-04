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

class AdminConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\AdminConfig
     */
    protected $adminConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\Stdlib\String
     */
    protected $stringHelperMock;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $this->stringHelperMock = $this->getMock('\Magento\Framework\Stdlib\String', [], [], '', false, false);
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
        $this->appState = $this->getMock('\Magento\Framework\App\State',
            ['isInstalled'], [], '', false, false);
        $this->appState->expects($this->atLeastOnce())->method('isInstalled')->will($this->returnValue(true));
        $this->filesystem = $this->getMock('\Magento\Framework\App\Filesystem', [], [], '', false, false);
    }

    public function testSetCookiePathNonDefault()
    {
        $mockFrontnameResolver = $this->getMockBuilder('\Magento\Backend\App\Area\FrontNameResolver')
            ->disableOriginalConstructor()
            ->getMock();

        $mockFrontnameResolver
            ->method('getFrontName')
            ->will($this->returnValue('backend'));

        $config = new \Magento\Framework\Session\Config(
            $this->configMock,
            $this->stringHelperMock,
            $this->requestMock,
            $this->appState,
            $this->filesystem,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $adminConfig = new \Magento\Backend\AdminConfig(
            $mockFrontnameResolver,
            $config,
            $this->requestMock
        );

        $this->assertEquals('/backend', $adminConfig->getCookiePath());
    }
}
