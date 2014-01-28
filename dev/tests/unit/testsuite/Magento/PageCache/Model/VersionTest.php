<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model;

/**
 * Class VersionTest
 * @package Magento\PageCache\Model
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Cookie mock
     *
     * @var \Magento\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cookieMock;

    /**
     * Request mock
     *
     * @var \Magento\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Version instance
     *
     * @var Version
     */
    private $versionInstance;

    /**
     * Create cookie and request mock, version instance
     */
    public function setUp() {
        $this->cookieMock = $this->getMock('Magento\Stdlib\Cookie', array('set'), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array('isPost'), array(), '', false);
        $this->versionInstance =  new Version($this->cookieMock, $this->requestMock);
    }

    /**
     * Handle private content version cookie
     * Set cookie if it is not set.
     * Increment version on post requests.
     * In all other cases do nothing.
     */
    /**
     * @dataProvider processProvider
     * @param bool $isPost
     */
    public function testProcess($isPost)
    {
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue($isPost));
        if ($isPost) {
            $this->cookieMock->expects($this->once())->method('set');
        }
        $this->versionInstance->process();
    }

    /**
     * Data provider for testProcess
     * @return array
     */
    public function processProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }
}
