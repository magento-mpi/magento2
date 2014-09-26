<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $currencyMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ratesFactoryMock;

    /**
     * @var \Magento\Reward\Helper\Data
     */
    protected $subject;

    protected function setUp()
    {
        $contextMock = $this->getMock('\Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->configMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->scopeConfigMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->currencyMock = $this->getMock('\Magento\Framework\Locale\CurrencyInterface');
        $this->ratesFactoryMock = $this->getMock(
            '\Magento\Reward\Model\Resource\Reward\Rate\CollectionFactory',
            [],
            [],
            '',
            false
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManagerHelper->getObject(
            '\Magento\Reward\Helper\Data',
            [
                'storeManager' => $this->storeManagerMock,
                'context' => $contextMock,
                'scopeConfig' => $this->scopeConfigMock,
                'config' => $this->configMock,
                'localeCurrency' => $this->currencyMock,
                'ratesFactory' => $this->ratesFactoryMock
            ]
        );
    }

    public function testIsEnabled()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(\Magento\Reward\Helper\Data::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->willReturn(true);
        $this->assertTrue($this->subject->isEnabled());
    }

    public function testGetConfigValue()
    {
        $websiteId = 2;
        $code = 'website_code';
        $configValue = 'config_value';
        $section = 'section';
        $field = 'field';

        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->with($websiteId)
            ->willReturn($websiteMock);
        $websiteMock->expects($this->once())->method('getCode')->willReturn($code);

        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with($section . $field, 'website', $code)
            ->willReturn($configValue);

        $this->assertEquals($configValue, $this->subject->getConfigValue($section, $field, $websiteId));
    }

    public function testGetGeneralConfig()
    {
        $websiteId = 2;
        $code = 'website_code';
        $configValue = 'config_value';
        $section = \Magento\Reward\Helper\Data::XML_PATH_SECTION_GENERAL;
        $field = 'field';

        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->with($websiteId)
            ->willReturn($websiteMock);
        $websiteMock->expects($this->once())->method('getCode')->willReturn($code);

        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with($section . $field, 'website', $code)
            ->willReturn($configValue);

        $this->assertEquals($configValue, $this->subject->getGeneralConfig($field, $websiteId));
    }

    public function testGetPointsConfig()
    {
        $websiteId = 2;
        $code = 'website_code';
        $configValue = 'config_value';
        $section = \Magento\Reward\Helper\Data::XML_PATH_SECTION_POINTS;
        $field = 'field';

        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->with($websiteId)
            ->willReturn($websiteMock);
        $websiteMock->expects($this->once())->method('getCode')->willReturn($code);

        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with($section . $field, 'website', $code)
            ->willReturn($configValue);

        $this->assertEquals($configValue, $this->subject->getPointsConfig($field, $websiteId));
    }

    public function testGetNotificationConfig()
    {
        $websiteId = 2;
        $code = 'website_code';
        $configValue = 'config_value';
        $section = \Magento\Reward\Helper\Data::XML_PATH_SECTION_NOTIFICATIONS;
        $field = 'field';

        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->with($websiteId)
            ->willReturn($websiteMock);
        $websiteMock->expects($this->once())->method('getCode')->willReturn($code);

        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with($section . $field, 'website', $code)
            ->willReturn($configValue);

        $this->assertEquals($configValue, $this->subject->getNotificationConfig($field, $websiteId));
    }

    /**
     * @param int $args
     * @param string $expectedResult
     *
     * @dataProvider formatPointsDeltaDataProvider
     */
    public function testFormatPointsDelta($points, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->subject->formatPointsDelta($points));
    }

    /**
     * @return array
     */
    public function formatPointsDeltaDataProvider()
    {
        return [
            ['points' => -100, 'expectedResult' => '-100'],
            ['points' => 100, 'expectedResult' => '100'],
        ];
    }

    public function testFormatAmountIfAmountIsNull()
    {

    }
}
