<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Cms\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Block
 */
class Block implements SetupInterface
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var Block\Converter
     */
    protected $converter;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $fixtures;

    /**
     * @var \Magento\Catalog\Service\V1\Category\WriteServiceInterface
     */
    protected $categoryWriteService;

    /**
     * @var \Magento\Catalog\Service\V1\Data\CategoryBuilder
     */
    protected $categoryDataBuilder;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param Block\Converter $converter
     * @param \Magento\Catalog\Service\V1\Category\WriteServiceInterface $categoryWriteService
     * @param \Magento\Catalog\Service\V1\Data\CategoryBuilder $categoryDataBuilder
     * @param array $fixtures
     */
    function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        Block\Converter $converter,
        \Magento\Catalog\Service\V1\Category\WriteServiceInterface $categoryWriteService,
        \Magento\Catalog\Service\V1\Data\CategoryBuilder $categoryDataBuilder,
        $fixtures = [
            'Cms/Block/categories_static_blocks.csv'
        ]

    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->blockFactory = $blockFactory;
        $this->converter = $converter;
        $this->categoryWriteService = $categoryWriteService;
        $this->categoryDataBuilder = $categoryDataBuilder;
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing CMS blocks\n";
        foreach ($this->fixtures as $file) {
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                $data = $this->converter->convertRow($row);
                $cmsBlock = $this->saveCmsBlock($data['block']);
                if (!empty($data['category_id'])) {
                    $this->setCategoryLandingPage($cmsBlock->getId(), $data['category_id']);
                }
                $cmsBlock->unsetData();
                echo '.';
            }
        }
        echo "\n";
    }

    /**
     * @param $data
     * @return \Magento\Cms\Model\Block
     * @throws \Exception
     */
    public function saveCmsBlock($data)
    {
        $cmsBlock = $this->blockFactory->create();
        $cmsBlock->getResource()->load($cmsBlock, $data['identifier']);
        if(!$cmsBlock->getData()) {
            $cmsBlock->setData($data);
            $cmsBlock->setStores(array('0'));
            $cmsBlock->setIsActive(1);
            $cmsBlock->save();
        }
        return $cmsBlock;
    }
    /**
     * @param $blockId
     * @param $categoryId
     */
    public function setCategoryLandingPage($blockId, $categoryId)
    {
        $categoryCms = array(
            'landing_page' => $blockId,
            'display_mode' => 'PRODUCTS_AND_PAGE'
        );
        if (!empty($categoryId)) {
            $updateCategoryData = $this->categoryDataBuilder->populateWithArray($categoryCms)->create();
            $this->categoryWriteService->update($categoryId, $updateCategoryData);
        }
    }
}
