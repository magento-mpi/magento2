<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Helper;

/**
 * Carrier helper test
 */
class CarrierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Shipping Carrier helper
     *
     * @var \Magento\Shipping\Helper\Carrier
     */
    protected $helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeConfig;

    public function setUp()
    {
        $this->storeConfig = $this->getMock('Magento\App\Config\ScopeConfigInterface');
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helper = $objectManagerHelper->getObject('Magento\Shipping\Helper\Carrier', [
            'context' => $this->getMock('Magento\App\Helper\Context', [], [], '', false),
            'locale' => $this->getMock('Magento\LocaleInterface'),
            'storeConfig' => $this->storeConfig,
        ]);
    }

    /**
     * @param array $result
     * @param array $carriers
     * @dataProvider getOnlineCarrierCodesDataProvider
     */
    public function testGetOnlineCarrierCodes($result, $carriers)
    {
        $this->storeConfig->expects($this->once())->method('getValue')
            ->with('carriers', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
            ->will($this->returnValue($carriers));
        $this->assertEquals($result, $this->helper->getOnlineCarrierCodes());
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getOnlineCarrierCodesDataProvider()
    {
        return [
            [[], ['carrier1' => []]],
            [[], ['carrier1' => ['is_online' => 0]]],
            [['carrier1'], ['carrier1' => ['is_online' => 1], 'carrier2' => ['is_online' => 0]]],
        ];
    }

    public function testGetCarrierConfigValue()
    {
        $carrierCode = 'carrier1';
        $configPath = 'title';
        $configValue = 'some title';
        $this->storeConfig->expects($this->once())->method('getValue')
            ->with(sprintf('carriers/%s/%s', $carrierCode, $configPath),
                \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
            ->will($this->returnValue($configValue));
        $this->assertEquals($configValue, $this->helper->getCarrierConfigValue($carrierCode, $configPath));
    }
}
