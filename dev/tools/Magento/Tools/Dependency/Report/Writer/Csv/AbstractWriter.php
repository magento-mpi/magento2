<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Writer\Csv;

use Magento\Tools\Dependency\Config;
use Magento\Tools\Dependency\Report\WriterInterface;

/**
 * Abstract csv file writer for reports
 */
abstract class AbstractWriter implements WriterInterface
{
    /**
     * Csv cell delimiter
     *
     * @var string
     */
    protected $delimiter;

    /**
     * Writer constructor
     *
     * @param string $delimiter
     */
    public function __construct($delimiter = ';')
    {
        $this->delimiter = $delimiter;
    }

    /**
     * Template method. Main algorithm
     *
     * {@inheritdoc}
     */
    public function write(Config $config, $filename)
    {
        $this->writeToFile($this->prepareData($config), $filename);
    }

    /**
     * Template method. Prepare data step
     *
     * @param \Magento\Tools\Dependency\Config $config
     * @return array
     */
    abstract protected function prepareData($config);

    /**
     * Template method. Write to file step
     *
     * @param array $data
     * @param string $filename
     */
    protected function writeToFile($data, $filename)
    {
        $fp = fopen($filename, 'w');
        foreach ($data as $row) {
            fputcsv($fp, $row, $this->delimiter);
        }
        fclose($fp);
    }
}
