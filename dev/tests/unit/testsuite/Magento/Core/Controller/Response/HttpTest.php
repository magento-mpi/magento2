<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\App\ResponseInterface
 */
namespace Magento\Core\Controller\Response;

class HttpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\CookieManager
     */
    protected $cookieManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Http\Context
     */
    protected $contextMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->cookieMetadataFactoryMock = $this->getMockBuilder(
            'Magento\Framework\Stdlib\Cookie\CookieMetadataFactory'
        )->disableOriginalConstructor()->getMock();
        $this->cookieManagerMock = $this->getMockBuilder('Magento\Framework\Stdlib\CookieManager')
            ->disableOriginalConstructor()->getMock();
        $this->contextMock = $this->getMockBuilder('Magento\Framework\App\Http\Context')->disableOriginalConstructor()
            ->getMock();
        $this->response = $objectManager->getObject(
            'Magento\Framework\App\Response\Http',
            [
                'cookieManager' => $this->cookieManagerMock,
                'cookieMetadataFactory' => $this->cookieMetadataFactoryMock,
                'context' => $this->contextMock
            ]
        );
        $this->response->headersSentThrowsException = false;

    }

    /**

     * Test for getHeader method
     *
     * @dataProvider headersDataProvider
     * @covers \Magento\Framework\App\Response\Http::getHeader
     * @param string $header
     */
    public function testGetHeaderExists($header)
    {
        $this->response->setHeader($header['name'], $header['value'], $header['replace']);
        $this->assertEquals($header, $this->response->getHeader($header['name']));
    }

    /**
     * Data provider for testGetHeader
     *
     * @return array
     */
    public function headersDataProvider()
    {
        return [
            [['name' => 'X-Frame-Options', 'value' => 'SAMEORIGIN', 'replace' => true]],
            [['name' => 'Test2', 'value' => 'Test2', 'replace' => false]]
        ];
    }

    /**
     * Test for getHeader method. Validation for attempt to get not existing header
     *
     * @covers \Magento\Framework\App\Response\Http::getHeader
     */
    public function testGetHeaderNotExists()
    {
        $this->response->setHeader('Name', 'value', true);
        $this->assertFalse($this->response->getHeader('Wrong name'));
    }
}
