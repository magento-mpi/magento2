<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Pricing;

use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Pricing\Object\SaleableInterface;

class AdjustmentTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    /**
     * @param int $configValue
     * @param bool $isShippingPriceExcludeTax
     * @param bool $expectedResult
     * @dataProvider isIncludedInBasePriceDataProvider
     */
    public function testIsIncludedInBasePrice($configValue, $isShippingPriceExcludeTax, $expectedResult)
    {
        // Instantiate objects
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Core\Model\StoreManagerInterface $storeManager */
        $storeManager = $objectManager->get('Magento\Core\Model\StoreManagerInterface');

        /** @var \Magento\Tax\Model\Config $config */
        $config = $objectManager->get('Magento\Tax\Model\Config');

        /** @var \Magento\Tax\Pricing\Adjustment $model */
        $model = $objectManager->create('Magento\Tax\Pricing\Adjustment');

        // Set fixtures
        $storeManager->getStore()
            ->setConfig(\Magento\Tax\Model\Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $configValue);
        $config->setNeedUseShippingExcludeTax($isShippingPriceExcludeTax);

        // Run tested method
        $result = $model->isIncludedInBasePrice();

        // Check expectations
        $this->assertInternalType('bool', $result);
        $this->assertEquals($expectedResult, $result);
    }

    public function isIncludedInBasePriceDataProvider()
    {
        return [
            [0, 0, false],
            [0, 1, true],
            [1, 0, true],
            [1, 1, true],
        ];
    }

    /**
     * @magentoAppIsolation enabled
     * @dataProvider isIncludedInDisplayPriceDataProvider
     */
    public function testIsIncludedInDisplayPrice($configValue, $expectedResult)
    {
        // Instantiate objects
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Core\Model\StoreManagerInterface $storeManager */
        $storeManager = $objectManager->get('Magento\Core\Model\StoreManagerInterface');

        /** @var \Magento\Tax\Pricing\Adjustment $model */
        $model = $objectManager->create('Magento\Tax\Pricing\Adjustment');

        /** @var SaleableInterface|\PHPUnit_Framework_MockObject_MockObject $taxHelper */
        $object = $this->getMockBuilder('Magento\Pricing\Object\SaleableInterface')->getMock();

        // Set fixtures
        $storeManager->getStore()
            ->setConfig(\Magento\Tax\Model\Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, $configValue);

        // Run tested method
        $result = $model->isIncludedInDisplayPrice($object);

        // Check expectations
        $this->assertInternalType('bool', $result);
        $this->assertEquals($expectedResult, $result);
    }

    public function isIncludedInDisplayPriceDataProvider()
    {
        return [
            [\Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX, false],
            [\Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX, true],
            [\Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH, true],
            [256, false],   //random number
        ];
    }
}
