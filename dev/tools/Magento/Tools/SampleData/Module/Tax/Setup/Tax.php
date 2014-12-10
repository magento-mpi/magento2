<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\Tax\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Tax
 */
class Tax implements SetupInterface
{
    /**
     * @var \Magento\Tax\Api\TaxRuleRepositoryInterface
     */
    protected $taxRuleRepository;

    /**
     * @var \Magento\Tax\Api\Data\TaxRuleDataBuilder
     */
    protected $ruleBuilder;

    /**
     * @var \Magento\Tax\Api\TaxRateRepositoryInterface
     */
    protected $taxRateRepository;

    /**
     * @var \Magento\Tax\Api\Data\TaxRateDataBuilder
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
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Tax\Api\TaxRuleRepositoryInterface $taxRuleRepository
     * @param \Magento\Tax\Api\Data\TaxRuleDataBuilder $ruleBuilder
     * @param \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository
     * @param \Magento\Tax\Api\Data\TaxRateDataBuilder $taxRateBuilder
     * @param \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Tools\SampleData\Logger $logger
     */
    public function __construct(
        \Magento\Tax\Api\TaxRuleRepositoryInterface $taxRuleRepository,
        \Magento\Tax\Api\Data\TaxRuleDataBuilder $ruleBuilder,
        \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository,
        \Magento\Tax\Api\Data\TaxRateDataBuilder $taxRateBuilder,
        \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Tools\SampleData\Logger $logger
    ) {
        $this->taxRuleRepository = $taxRuleRepository;
        $this->ruleBuilder = $ruleBuilder;
        $this->taxRateRepository = $taxRateRepository;
        $this->taxRateBuilder = $taxRateBuilder;
        $this->taxRateFactory = $taxRateFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing taxes' . PHP_EOL);
        $fixtureFile = 'Tax/tax_rate.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $data) {
            $this->taxRateBuilder->setCode($data['code'])
                ->setTaxCountryId($data['tax_country_id'])
                ->setTaxRegionId($data['tax_region_id'])
                ->setTaxPostcode($data['tax_postcode'])
                ->setRate($data['rate']);
            $taxData = $this->taxRateBuilder->create();
            $this->taxRateRepository->save($taxData);
            $this->logger->log('.');
        }

        $fixtureFile = 'Tax/tax_rule.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $data) {
            $taxRate = $this->taxRateFactory->create()->loadByCode($data['tax_rate']);
            $this->ruleBuilder->setCode($data['code'])
                ->setTaxRateIds([$taxRate->getId()])
                ->setCustomerTaxClassIds([$data['tax_customer_class']])
                ->setProductTaxClassIds([$data['tax_product_class']])
                ->setPriority($data['priority'])
                ->setCalculateSubtotal($data['calculate_subtotal'])
                ->setPosition($data['position']);
            $taxRule = $this->ruleBuilder->create();
            $this->taxRuleRepository->save($taxRule);
            $this->logger->log('.');
        }
        $this->logger->log(PHP_EOL);
    }
}
