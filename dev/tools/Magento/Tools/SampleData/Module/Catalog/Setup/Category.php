<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Catalog\Setup;

use Magento\Framework\File\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

class Category implements SetupInterface
{
    protected $categoryFactory;

    protected $writeService;

    protected $categoryDataBuilder;

    protected $categoryTreeFactory;

    protected $resourceCategoryTreeFactory;

    protected $categoryCollectionFactory;

    protected $fixtureHelper;

    protected $csvReaderFactory;

    protected $categoryNameCategoryPair;

    protected $categoryTree;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Service\V1\Category\WriteServiceInterface $writeService,
        \Magento\Catalog\Service\V1\Data\CategoryBuilder $categoryDataBuilder,
        \Magento\Catalog\Service\V1\Data\Category\TreeFactory $categoryTreeFactory,
        \Magento\Catalog\Model\Resource\Category\TreeFactory $resourceCategoryTreeFactory,
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryCollectionFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->writeService = $writeService;
        $this->categoryDataBuilder = $categoryDataBuilder;
        $this->categoryTreeFactory = $categoryTreeFactory;
        $this->resourceCategoryTreeFactory = $resourceCategoryTreeFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
    }

    public function run()
    {
        echo "Installing categories\n";

        $fileName = $this->fixtureHelper->getPath('Catalog/categories.csv');
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
        foreach($csvReader as $row) {
            $category = $this->getCategoryByPath($row['path'] . '/' . $row['name']);
            if (!$category) {
                $parentCategory = $this->getCategoryByPath($row['path']);
                $data = [
                    'parent_id' => $parentCategory ? $parentCategory->getId() : null,
                    'name' => $row['name'],
                    'active' => $row['active'],
                    'anchor' => $row['anchor'],
                    'include_in_menu' => $row['include_in_menu'],
                ];

                $categoryData = $this->categoryDataBuilder->populateWithArray($data)->create();
                $this->writeService->create($categoryData);
            }
            echo '.';
        }
        echo "\n";
    }

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
