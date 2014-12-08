<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Widget\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for Widget module
 */
class CmsBlock implements SetupInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $fixtures;

    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $widgetFactory;

    /**
     * @var \Magento\Core\Model\Resource\Theme\Collection
     */
    protected $themeCollection;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $cmsBlockCollection;

    /**
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Core\Model\Resource\Theme\Collection $themeCollection
     * @param \Magento\Cms\Model\BlockFactory $cmsBlockCollection
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory
     * @param \Magento\Tools\SampleData\Logger $logger
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Core\Model\Resource\Theme\Collection $themeCollection,
        \Magento\Cms\Model\BlockFactory $cmsBlockCollection,
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory,
        \Magento\Tools\SampleData\Logger $logger,
        $fixtures = []
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->widgetFactory = $widgetFactory;
        $this->themeCollection = $themeCollection;
        $this->cmsBlockCollection = $cmsBlockCollection;
        $this->categoryFactory = $categoryFactory;
        $this->fixtures = $fixtures;
        if (empty($this->fixtures)) {
            $this->fixtures = $this->fixtureHelper->getDirectoryFiles('Widget');
        }
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing Widgets' . PHP_EOL);
        $pageGroupConfig = [
            'pages' => [
                'block' => '',
                'for' => 'all',
                'layout_handle' => 'default',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => '',
            ],
            'all_pages' => [
                'block' => '',
                'for' => 'all',
                'layout_handle' => 'default',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => '',
            ],
            'anchor_categories' => [
                'entities' => '',
                'block' => '',
                'for' => 'all',
                'is_anchor_only' => 0,
                'layout_handle' => 'catalog_category_view_type_layered',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => '',
            ],
        ];

        foreach ($this->fixtures as $file) {
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            foreach ($csvReader as $row) {
                //$block = $this->cmsBlockCollection->getItemByColumnValue('identifier', $row['block_identifier']);
                $block = $this->cmsBlockCollection->create()->load($row['block_identifier'], 'identifier');
                if (!$block) {
                    continue;
                }
                $widgetInstance = $this->widgetFactory->create();

                $code = $row['type_code'];
                $themeId = $this->themeCollection->getThemeByFullPath($row['theme_path'])->getId();
                $type = $widgetInstance->getWidgetReference('code', $code, 'type');
                $pageGroup = [];
                $group = $row['page_group'];
                $pageGroup['page_group'] = $group;
                $pageGroup[$group] = array_merge($pageGroupConfig[$group], unserialize($row['group_data']));
                if (!empty($pageGroup[$group]['entities'])) {
                    $pageGroup[$group]['entities'] = $this->getCategoryByUrlKey(
                        $pageGroup[$group]['entities']
                    )->getId();
                }

                $widgetInstance->setType($type)->setCode($code)->setThemeId($themeId);
                $widgetInstance->setTitle($row['title'])
                    ->setStoreIds([\Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->setWidgetParameters(['block_id' => $block->getId()])
                    ->setPageGroups([$pageGroup]);
                $widgetInstance->save();
                $this->logger->log('.');
            }
        }
        $this->logger->log(PHP_EOL);
    }

    /**
     * @param string $urlKey
     * @return \Magento\Framework\Object
     */
    protected function getCategoryByUrlKey($urlKey)
    {
        $category = $this->categoryFactory->create()
            ->addAttributeToFilter('url_key', $urlKey)
            ->addUrlRewriteToResult()
            ->getFirstItem();
        return $category;
    }
}
