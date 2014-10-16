<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\TargetRule\Setup;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\Helper\PostInstaller;
use Magento\TargetRule\Model\RuleFactory as RuleFactory;

/**
 * Class Setup
 * Launches setup of sample data for RecurringPayment module
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
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @var \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface
     */
    protected $categoryReadService;

    /**
     * @param CsvReaderFactory $csvReaderFactory
     * @param FixtureHelper $fixtureHelper
     * @param RuleFactory $ruleFactory
     * @param \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService
     * @param PostInstaller $postInstaller
     */
    public function __construct(
        CsvReaderFactory $csvReaderFactory,
        FixtureHelper $fixtureHelper,
        RuleFactory $ruleFactory,
        \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService,
        PostInstaller $postInstaller
    ) {
        $this->csvReaderFactory = $csvReaderFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->ruleFactory = $ruleFactory;
        $this->categoryReadService = $categoryReadService;
        $this->postInstaller = $postInstaller;
    }

    /**
     * @param array $categoryPath
     * @return array|null
     */
    protected function getConditionFromCategory($categoryPath)
    {
        $categoryId = null;
        $tree = $this->categoryReadService->tree();
        foreach ($categoryPath as $categoryName) {
            $categoryId = null;
            foreach ($tree->getChildren() as $child) {
                if ($child->getName() == $categoryName) {
                    $tree = $child;
                    $categoryId = $child->getId();
                    break;
                }
            }
        }
        if (!$categoryId) {
            return null;
        }
        return [
          'type' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes',
          'attribute' => 'category_ids',
          'operator' => '==',
          'value' => $categoryId
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing related product rules\n";
        $this->postInstaller->removeSetupResourceType('Magento\Tools\SampleData\Module\Catalog\Setup\ProductLink');
        $entityFileAssociation = [
            \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS => 'related',
            \Magento\TargetRule\Model\Rule::UP_SELLS => 'upsell',
            \Magento\TargetRule\Model\Rule::CROSS_SELLS => 'crosssell'
        ];

        $sortOrder = 0;
        foreach ($entityFileAssociation as $linkTypeId => $linkType) {
            $fileName = 'TargetRule/' . $linkType . '.csv';
            $fileName = $this->fixtureHelper->getPath($fileName);
            if (!$fileName) {
                continue;
            }
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                /** @var \Magento\TargetRule\Model\Rule $rule */
                $rule = $this->ruleFactory->create();
                if ($rule->getResourceCollection()->addFilter('name', $row['name'])->getSize() > 0) {
                    continue;
                }
                $sourceCategory = $this->getConditionFromCategory(
                    array_filter(explode("\n", $row['source_category']))
                );
                $targetCategory = $this->getConditionFromCategory(
                    array_filter(explode("\n", $row['target_category']))
                );
                if (!$sourceCategory || !$targetCategory) {
                    continue;
                }
                $ruleConditions = [
                    'conditions' => [
                        1 => [
                            'type' => 'Magento\TargetRule\Model\Rule\Condition\Combine',
                            'aggregator' => 'all',
                            'value' => '1',
                            'new_child' => ''
                        ],
                        '1--1' => $sourceCategory
                    ],
                    'actions' => [
                        1 => [
                            'type' => 'Magento\TargetRule\Model\Actions\Condition\Combine',
                            'aggregator' => 'all',
                            'value' => '1',
                            'new_child' => ''
                        ],
                        '1--1' => $targetCategory
                    ]
                ];
                if (!empty($row['conditions'])) {
                    $index = 2;
                    foreach (array_filter(explode("\n", $row['conditions'])) as $condition) {
                        $ruleConditions['actions']['1--' . $index] = unserialize($condition);
                        $index++;
                    }
                }
                $rule->setName($row['name'])
                    ->setApplyTo($linkTypeId)
                    ->setIsActive(1)
                    ->setSortOrder($sortOrder)
                    ->setPositionsLimit(empty($row['limit']) ? 0 : $row['limit']);
                $sortOrder++;
                $rule->loadPost($ruleConditions);
                $rule->save();
                echo '.';
            }
        }
        echo "\n";
    }
}
