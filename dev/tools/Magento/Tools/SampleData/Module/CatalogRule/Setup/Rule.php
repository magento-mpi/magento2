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
     * @var \Magento\CatalogRule\Model\Rule\JobFactory
     */
    protected $jobFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @param CsvReaderFactory $csvReaderFactory
     * @param FixtureHelper $fixtureHelper
     * @param RuleFactory $ruleFactory
     * @param \Magento\CatalogRule\Model\Rule\JobFactory $jobFactory
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     */
    public function __construct(
        CsvReaderFactory $csvReaderFactory,
        FixtureHelper $fixtureHelper,
        RuleFactory $ruleFactory,
        \Magento\CatalogRule\Model\Rule\JobFactory $jobFactory,
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory
    ) {
        $this->csvReaderFactory = $csvReaderFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->ruleFactory = $ruleFactory;
        $this->jobFactory = $jobFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->groupFactory = $groupFactory;
        $this->websiteFactory = $websiteFactory;
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
        foreach ($csvReader as $row) {
            $row['customer_group_ids'] = $this->getGroupIds();
            $row['website_ids'] = $this->getWebsiteIds();
            $row['conditions_serialized'] = $this->convertSerializedData($row['conditions_serialized']);
            $row['actions_serialized'] = $this->convertSerializedData($row['actions_serialized']);
            $ruleModel = $this->ruleFactory->create();
            $ruleModel->loadPost($row);
            $ruleModel->save();
            echo '.';
        }
        $ruleJob = $this->jobFactory->create();
        $ruleJob->applyAll();
        echo "\n";
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function convertSerializedData($data)
    {
        $regexp = '/\%(.*?)\%/';
        preg_match_all($regexp, $data, $matches);
        $replacement = null;
        foreach ($matches[1] as $matchedId => $matchedItem) {
            $extractedData = array_filter(explode(",", $matchedItem));
            foreach ($extractedData as $extractedItem) {
                $separatedData = array_filter(explode('=', $extractedItem));
                if ($separatedData[0] == 'url_key') {
                    if (!$replacement) {
                        $replacement = $this->getCategoryReplacement($separatedData[1]);
                    } else {
                        $replacement .= ',' . $this->getCategoryReplacement($separatedData[1]);
                    }
                }
            }
            if (!empty($replacement)) {
                $data = preg_replace('/' . $matches[0][$matchedId] . '/', serialize($replacement), $data);
            }
        }
        return $data;
    }

    /**
     * @param string $urlKey
     * @return mixed|null
     */
    protected function getCategoryReplacement($urlKey)
    {
        $categoryCollection = $this->categoryCollectionFactory->create();
        $category = $categoryCollection->addAttributeToFilter('url_key', $urlKey)->getFirstItem();
        $categoryId = null;
        if (!empty($category)) {
            $categoryId = $category->getId();
        }
        return $categoryId;
    }

    /**
     * @return array
     */
    public function getGroupIds()
    {
        $groupsIds = array();
        $collection = $this->groupFactory->create()->getCollection();
        foreach ($collection as $group) {
            $groupsIds[] = $group->getId();
        }
        return $groupsIds;
    }

    /**
     * @return array
     */
    public function getWebsiteIds()
    {
        $websiteIds = array();
        $collection = $this->websiteFactory->create()->getCollection();
        foreach ($collection as $website) {
            $websiteIds[] = $website->getId();
        }
        return $websiteIds;
    }
}
