<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\PageCache;

use Magento\TestFramework\ObjectManager;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Version instance
     *
     * @var Version
     */
    protected $version;

    /**
     * Cookie manager mock
     *
     * @var \Magento\Framework\Stdlib\CookieManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieManagerMock;

    /**
     * Cookie manager mock
     *
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieMetadataFactoryMock;

    /**
     * Request mock
     *
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * Create cookie and request mock, version instance
     */
    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->cookieManagerMock = $this->getMockBuilder('Magento\Framework\Stdlib\CookieManager')
            ->disableOriginalConstructor()->getMock();
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()->getMock();
        $this->cookieMetadataFactoryMock = $this->getMockBuilder(
            'Magento\Framework\Stdlib\Cookie\CookieMetadataFactory'
        )
            ->disableOriginalConstructor()->getMock();
        $this->version = $objectManager->getObject(
            'Magento\Framework\App\PageCache\Version',
            [
                'cookieManager' => $this->cookieManagerMock,
                'cookieMetadataFactory' => $this->cookieMetadataFactoryMock,
                'request' => $this->requestMock
            ]
        );
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
        $this->requestMock->expects($this->once())->method('isPost')->will($this->returnValue($isPost));
        if ($isPost) {
            $publicCookieMetadataMock = $this->getMock('Magento\Framework\Stdlib\Cookie\PublicCookieMetadata');
            $publicCookieMetadataMock->expects($this->once())
                ->method('setPath')
                ->with('/')
                ->will($this->returnSelf());

            $publicCookieMetadataMock->expects($this->once())
                ->method('setDuration')
                ->with(Version::COOKIE_PERIOD)
                ->will($this->returnSelf());

            $publicCookieMetadataMock->expects($this->once())
                ->method('setHttpOnly')
                ->with(false)
                ->will($this->returnSelf());

            $this->cookieMetadataFactoryMock->expects($this->once())
                ->method('createPublicCookieMetadata')
                ->with()
                ->will(
                    $this->returnValue($publicCookieMetadataMock)
                );

            $this->cookieManagerMock->expects($this->once())
                ->method('setPublicCookie');
        }
        $this->version->process();
    }

    /**
     * Data provider for testProcess
     *
     * @return array
     */
    public function processProvider()
    {
        return [
            "post" => [true],
            "notPost" => [false]
        ];
    }
}
