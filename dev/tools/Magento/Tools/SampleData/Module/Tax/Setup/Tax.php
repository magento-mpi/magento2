<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Tax\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Tax
 *
 * @package Magento\Tools\SampleData\Module\Tax\Setup
 */
class Tax implements SetupInterface
{
    /**
     * @var \Magento\Tax\Service\V1\TaxRuleServiceInterface
     */
    protected $ruleService;

    /**
     * @var \Magento\Tax\Service\V1\Data\TaxRuleBuilder
     */
    protected $ruleBuilder;

    /**
     * @var \Magento\Tax\Service\V1\TaxRateServiceInterface
     */
    protected $taxRateService;

    /**
     * @var \Magento\Tax\Service\V1\Data\TaxRateBuilder
     */
    protected $taxRateBuilder;

    /**
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    protected $taxRateFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @param \Magento\Tax\Service\V1\TaxRuleServiceInterface $ruleService
     * @param \Magento\Tax\Service\V1\Data\TaxRuleBuilder $ruleBuilder
     * @param \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService
     * @param \Magento\Tax\Service\V1\Data\TaxRateBuilder $taxRateBuilder
     * @param \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     */
    public function __construct(
        \Magento\Tax\Service\V1\TaxRuleServiceInterface $ruleService,
        \Magento\Tax\Service\V1\Data\TaxRuleBuilder $ruleBuilder,
        \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService,
        \Magento\Tax\Service\V1\Data\TaxRateBuilder $taxRateBuilder,
        \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->ruleService = $ruleService;
        $this->ruleBuilder = $ruleBuilder;
        $this->taxRateService = $taxRateService;
        $this->taxRateBuilder = $taxRateBuilder;
        $this->taxRateFactory = $taxRateFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing taxes' . PHP_EOL;
        $fixtureFile = 'Tax/tax_rate.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $data) {
            $this->taxRateBuilder->setCode($data['code'])
                ->setCountryId($data['tax_country_id'])
                ->setRegionId($data['tax_region_id'])
                ->setPostcode($data['tax_postcode'])
                ->setPercentageRate($data['rate']);
            $taxData = $this->taxRateBuilder->create();
            $this->taxRateService->createTaxRate($taxData);
            echo '.';
        }

        $fixtureFile = 'Tax/tax_rule.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $data) {
            $taxRate = $this->taxRateFactory->create()->loadByCode($data['tax_rate']);
            $this->ruleBuilder->setCode($data['code'])
                ->setTaxRateIds([$taxRate->getId()])
                ->setCustomerTaxClassIds([$data['tax_customer_class']])
                ->setProductTaxClassIds([$data['tax_product_class']])
                ->setPriority($data['priority'])
                ->setCalculateSubtotal($data['calculate_subtotal'])
                ->setSortOrder($data['position']);
            $taxRule = $this->ruleBuilder->create();
            $this->ruleService->createTaxRule($taxRule);
            echo '.';
        }
        echo PHP_EOL;
    }
}
