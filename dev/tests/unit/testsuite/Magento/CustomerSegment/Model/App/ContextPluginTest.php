<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Model\App;

/**
 * Class ContextPluginTest
 */
class ContextPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\App\ContextPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\App\Http\Context $httpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var \Magento\LauncherInterface
     */
    protected $launcherMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session',
            array('getCustomerSegmentIds'), array(), '', false);
        $this->httpContextMock = $this->getMock('Magento\App\Http\Context',
            array(), array(), '', false);
        $this->launcherMock = $this->getMock('Magento\App\Http',
            array(), array(), '', false);
        $this->plugin = new \Magento\CustomerSegment\Model\App\ContextPlugin(
            $this->customerSessionMock,
            $this->httpContextMock
        );
    }

    /**
     * Test beforeLaunch
     */
    public function testBeforeLaunch()
    {
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerSegmentIds')
            ->will($this->returnValue(array(1, 2, 3)));

        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(
                $this->equalTo(\Magento\CustomerSegment\Helper\Data::CONTEXT_SEGMENT),
                $this->equalTo(array(1, 2, 3))
            )
            ->will($this->returnValue($this->httpContextMock));
        $this->assertNull($this->plugin->beforeLaunch($this->launcherMock));
    }
}
