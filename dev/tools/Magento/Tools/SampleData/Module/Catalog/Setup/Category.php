<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Catalog\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Category
 */
class Category implements SetupInterface
{
    /**
     * @var \Magento\Catalog\Service\V1\Category\WriteServiceInterface
     */
    protected $writeService;

    /**
     * @var \Magento\Catalog\Service\V1\Data\CategoryBuilder
     */
    protected $categoryDataBuilder;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Category\TreeFactory
     */
    protected $categoryTreeFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Category\TreeFactory
     */
    protected $resourceCategoryTreeFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;


    /**
     * @var categoryTree
     */
    protected $categoryTree;

    /**
     * @param \Magento\Catalog\Service\V1\Category\WriteServiceInterface $writeService
     * @param \Magento\Catalog\Service\V1\Data\CategoryBuilder $categoryDataBuilder
     * @param \Magento\Catalog\Service\V1\Data\Category\TreeFactory $categoryTreeFactory
     * @param \Magento\Catalog\Model\Resource\Category\TreeFactory $resourceCategoryTreeFactory
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     */
    public function __construct(
        \Magento\Catalog\Service\V1\Category\WriteServiceInterface $writeService,
        \Magento\Catalog\Service\V1\Data\CategoryBuilder $categoryDataBuilder,
        \Magento\Catalog\Service\V1\Data\Category\TreeFactory $categoryTreeFactory,
        \Magento\Catalog\Model\Resource\Category\TreeFactory $resourceCategoryTreeFactory,
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->writeService = $writeService;
        $this->categoryDataBuilder = $categoryDataBuilder;
        $this->categoryTreeFactory = $categoryTreeFactory;
        $this->resourceCategoryTreeFactory = $resourceCategoryTreeFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->moduleList = $moduleList;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing categories\n";

        foreach (array_keys($this->moduleList->getModules()) as $moduleName) {
            $fileName = substr($moduleName, strpos($moduleName, "_") + 1) . '/categories.csv';
            $fileName = $this->fixtureHelper->getPath($fileName);
            if (!$fileName) {
                continue;
            }
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                $category = $this->getCategoryByPath($row['path'] . '/' . $row['name']);
                if (!$category) {
                    $parentCategory = $this->getCategoryByPath($row['path']);
                    $data = [
                        'parent_id' => $parentCategory ? $parentCategory->getId() : null,
                        'name' => $row['name'],
                        'active' => $row['active'],
                        'is_anchor' => $row['is_anchor'],
                        'include_in_menu' => $row['include_in_menu'],
                        'url_key' => $row['url_key']
                    ];

                    $categoryData = $this->categoryDataBuilder->populateWithArray($data)->create();
                    $categoryId = $this->writeService->create($categoryData);
                    if (!empty($row['position'])) {
                        $positionData = array('position' => $row['position']);
                        $updateCategoryData = $this->categoryDataBuilder->populateWithArray($positionData)->create();
                        $this->writeService->update($categoryId, $updateCategoryData);
                    }
                }
                echo '.';
            }
        }

        echo "\n";
    }

    /**
     * Get category name by path
     *
     * @param string $path
     * @return mixed
     */
    protected function getCategoryByPath($path)
    {
        $names = array_filter(explode('/', $path));
        $tree = $this->getTree();
        foreach ($names as $name) {
            $tree = $this->findTreeChild($tree, $name);
            if (!$tree) {
                $tree = $this->findTreeChild($this->getTree(null, true), $name);
            }
            if (!$tree) {
                break;
            }
        }
        return $tree;
    }

    /**
     * Get child categories
     *
     * @param mixed $tree
     * @param string $name
     * @return mixed
     */
    protected function findTreeChild($tree, $name)
    {
        $foundChild = null;
        if ($name) {
            foreach ($tree->getChildren() as $child) {
                if ($child->getName() == $name) {
                    $foundChild = $child;
                    break;
                }
            }
        }
        return $foundChild;
    }

    /**
     * Get category tree
     *
     * @param null $rootNode
     * @param bool $reload
     * @return mixed
     */
    protected function getTree($rootNode = null, $reload = false)
    {
        if (!$this->categoryTree || $reload) {
            $this->categoryTree = $this->categoryTreeFactory->create([
                'categoryTree' => $this->resourceCategoryTreeFactory->create(),
                'categoryCollection' => $this->categoryCollectionFactory->create(),
            ]);

        }
        return $this->categoryTree->getTree($this->categoryTree->getRootNode($rootNode));
    }
}
