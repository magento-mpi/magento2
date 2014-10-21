<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Widget\Setup;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

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
     * @var \Magento\Cms\Model\Resource\Block\Collection
     */
    protected $cmsBlockCollection;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Core\Model\Resource\Theme\Collection $themeCollection
     * @param \Magento\Cms\Model\Resource\Block\Collection $cmsBlockCollection
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Core\Model\Resource\Theme\Collection $themeCollection,
        \Magento\Cms\Model\Resource\Block\Collection $cmsBlockCollection,
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory,
        $fixtures = array(
            'Widget/cmsblock.csv',
        )
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->widgetFactory = $widgetFactory;
        $this->themeCollection = $themeCollection;
        $this->cmsBlockCollection = $cmsBlockCollection;
        $this->categoryFactory = $categoryFactory;
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing Widgets\n";
        $pageGroupConfig = array(
            'pages' => array(
                'block' => '',
                'for' => 'all',
                'layout_handle' => 'default',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => ''
            ),
            'all_pages' => array(
                'block' => '',
                'for' => 'all',
                'layout_handle' => 'default',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => ''
            ),
            'anchor_categories' => array(
                'entities' => '',
                'block' => '',
                'for' => 'all',
                'is_anchor_only' => 0,
                'layout_handle' => 'catalog_category_view_type_layered',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => ''
            )
        );

        foreach ($this->fixtures as $file) {
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                $block = $this->cmsBlockCollection->getItemByColumnValue('identifier', $row['block_identifier']);
                if (!$block) {
                    continue;
                }
                $widgetInstance = $this->widgetFactory->create();

                $code = $row['type_code'];
                $themeId = $this->themeCollection->getThemeByFullPath($row['theme_path'])->getId();
                $type = $widgetInstance->getWidgetReference('code', $code, 'type');
                $pageGroup = array();
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
                    ->setStoreIds(array(0))
                    ->setWidgetParameters(array('block_id' => $block->getId()))
                    ->setPageGroups(array($pageGroup));
                $widgetInstance->save();
                echo '.';
            }
        }
        echo "\n";
    }

    /**
     * @param $urlKey
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
