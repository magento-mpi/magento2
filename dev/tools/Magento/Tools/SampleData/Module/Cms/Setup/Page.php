<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Cms\Setup;

use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Launches setup of sample data for CMS Page
 */
class Page implements SetupInterface
{
    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $pageFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $fixtures;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        $fixtures = array(
            'Cms/Page/pages.csv',
        )
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->pageFactory = $pageFactory;
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing CMS pages\n";

        foreach ($this->fixtures as $file) {
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                $this->pageFactory->create()
                    ->load($row['identifier'], 'identifier')
                    ->addData($row)
                    ->setStores(array(0))
                    ->save();
                echo '.';
            }
        }
        echo "\n";
    }
}
