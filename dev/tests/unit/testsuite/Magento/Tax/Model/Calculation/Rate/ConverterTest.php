<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation\Rate;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @param array $data
     * @dataProvider createTaxRateTitleDataProvider
     */
    public function testCreateTitlesFromServiceObject($data)
    {
        $taxRateBuilder = $this->objectManager->getObject(
            'Magento\Tax\Api\Data\TaxRateDataBuilder'
        );

        $taxRate = $taxRateBuilder->setTitles($data)->create();

        /** @var  $converter \Magento\Tax\Model\Calculation\Rate\Converter */
        $converter = $this->objectManager->getObject(
            'Magento\Tax\Model\Calculation\Rate\Converter'
        );

        $titles = $converter->createTitleArrayFromServiceObject($taxRate);
        foreach ($data as $expectedTitle) {
            $storeId = $expectedTitle->getStoreId();
            $this->assertTrue(isset($titles[$storeId]), "Title for store id {$storeId} was not set.");
            $this->assertEquals($expectedTitle->getValue(), $titles[$storeId]);
        }
    }

    public function createTaxRateTitleDataProvider()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $titleBuilder = $this->objectManager->getObject('Magento\Tax\Api\Data\TaxRateTitleDataBuilder');
        $titleBuilder->setValue('tax title');
        $titleBuilder->setStoreId(5);

        $title1 = $titleBuilder->create();

        $titleBuilder->setValue('tax title 2');
        $titleBuilder->setStoreId(1);

        $title2 = $titleBuilder->create();

        return [
            'no titles' => [
                []
            ],
            '1 title' => [
                [
                    $title1
                ]
            ],
            '2 title2' => [
                [
                  $title1,
                  $title2,
                ]
            ]
        ];
    }
}
