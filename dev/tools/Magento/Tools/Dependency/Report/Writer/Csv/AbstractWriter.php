<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Writer\Csv;

use Magento\Tools\Dependency\Report\Data\ConfigInterface;
use Magento\Tools\Dependency\Report\WriterInterface;

/**
 * Abstract csv file writer for reports
 */
abstract class AbstractWriter implements WriterInterface
{
    /**
     * Csv write object
     *
     * @var \Magento\File\Csv
     */
    protected $writer;

    /**
     * Writer constructor
     *
     * @param \Magento\File\Csv $writer
     */
    public function __construct($writer)
    {
        $this->writer = $writer;
    }

    /**
     * Template method. Main algorithm
     *
     * {@inheritdoc}
     */
    public function write($filename, ConfigInterface $config)
    {
        $this->writeToFile($filename, $this->prepareData($config));
    }

    /**
     * Template method. Prepare data step
     *
     * @param \Magento\Tools\Dependency\Report\Data\ConfigInterface $config
     * @return array
     */
    abstract protected function prepareData($config);

    /**
     * Template method. Write to file step
     *
     * @param string $filename
     * @param array $data
     */
    protected function writeToFile($filename, $data)
    {
        $this->writer->saveData($filename, $data);
    }
}
