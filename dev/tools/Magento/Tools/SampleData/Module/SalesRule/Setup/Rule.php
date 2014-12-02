<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\SalesRule\Setup;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SalesRule\Model\RuleFactory as RuleFactory;
use Magento\Tools\SampleData\Module\CatalogRule\Setup\Rule as CatalogRule;

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
     * @var CatalogRule
     */
    protected $catalogRule;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param CsvReaderFactory $csvReaderFactory
     * @param FixtureHelper $fixtureHelper
     * @param RuleFactory $ruleFactory
     * @param CatalogRule $catalogRule
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        CsvReaderFactory $csvReaderFactory,
        FixtureHelper $fixtureHelper,
        RuleFactory $ruleFactory,
        CatalogRule $catalogRule,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->csvReaderFactory = $csvReaderFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->ruleFactory = $ruleFactory;
        $this->catalogRule = $catalogRule;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing sales rules\n";
        $file = 'SalesRule/sales_rules.csv';
        $fileName = $this->fixtureHelper->getPath($file);
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'sku');
        if ($attribute->getIsUsedForPromoRules() == 0) {
            $attribute->setIsUsedForPromoRules('1')->save();
        }
        foreach ($csvReader as $row) {
            $row['customer_group_ids'] = $this->catalogRule->getGroupIds();
            $row['website_ids'] = $this->catalogRule->getWebsiteIds();
            $row['conditions_serialized'] = $this->catalogRule->convertSerializedData($row['conditions_serialized']);
            $row['actions_serialized'] = $this->catalogRule->convertSerializedData($row['actions_serialized']);
            $rule = $this->ruleFactory->create();
            $rule->loadPost($row);
            $rule->save();
            echo '.';
        }
        echo "\n";
    }
}
