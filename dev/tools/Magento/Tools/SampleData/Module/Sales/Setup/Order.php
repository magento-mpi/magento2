<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Sales\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Order
 */
class Order implements SetupInterface
{
    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Module\Sales\Setup\Order\Converter
     */
    protected $converter;

    /**
     * @var \Magento\Tools\SampleData\Module\Sales\Setup\Order\Processor
     */
    protected $orderProcessor;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Order\Converter $converter
     * @param Order\Processor $orderProcessor
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Order\Converter $converter,
        Order\Processor $orderProcessor,
        $fixtures = [
            'Sales/orders.csv'
        ]
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->converter = $converter;
        $this->orderProcessor = $orderProcessor;
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing orders\n";
        foreach ($this->fixtures as $file) {
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                $orderData = $this->converter->convertRow($row);
                $this->orderProcessor->createOrder($orderData);
                echo '.';
                }
            }
        echo "\n";
    }
}