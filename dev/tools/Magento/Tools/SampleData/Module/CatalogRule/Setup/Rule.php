<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\CatalogRule\Setup;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\Module\SalesRule\Setup\Rule as SalesRule;
use Magento\CatalogRule\Model\RuleFactory as RuleFactory;

/**
 * Class Rule
 */
class Rule implements SetupInterface
{
    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var SalesRule
     */
    protected $salesRule;

    /**
     * @var \Magento\CatalogRule\Model\Rule\JobFactory
     */
    protected $jobFactory;

    /**
     * @param CsvReaderFactory $csvReaderFactory
     * @param FixtureHelper $fixtureHelper
     * @param RuleFactory $ruleFactory
     * @param SalesRule $salesRule
     * @param \Magento\CatalogRule\Model\Rule\JobFactory $jobFactory
     */
    public function __construct(
        CsvReaderFactory $csvReaderFactory,
        FixtureHelper $fixtureHelper,
        RuleFactory $ruleFactory,
        SalesRule $salesRule,
        \Magento\CatalogRule\Model\Rule\JobFactory $jobFactory
    ) {
        $this->csvReaderFactory = $csvReaderFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->ruleFactory = $ruleFactory;
        $this->salesRule = $salesRule;
        $this->jobFactory = $jobFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing catalog rules\n";
        $file = 'CatalogRule/catalog_rules.csv';
        $fileName = $this->fixtureHelper->getPath($file);
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
        $ruleModel = $this->ruleFactory->create();
        foreach ($csvReader as $row) {
            $row['website_ids'] = unserialize($row['website_ids']);
            $row['customer_group_ids'] = unserialize($row['customer_group_ids']);
            $row['conditions_serialized'] = $this->salesRule->convertSerializedData($row['conditions_serialized']);
            $row['actions_serialized'] = $this->salesRule->convertSerializedData($row['actions_serialized']);
            $ruleModel->loadPost($row);
            $ruleModel->save();
            $ruleJob = $this->jobFactory->create();
            $ruleJob->applyAll();
            echo '.';
        }
        echo "\n";
    }
}
