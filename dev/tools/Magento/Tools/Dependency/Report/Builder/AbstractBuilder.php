<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Builder;

use Magento\Tools\Dependency\Report\BuilderInterface;
use Magento\Tools\Dependency\ParserInterface;
use Magento\Tools\Dependency\Report\WriterInterface;

/**
 *  Abstract report builder by config files
 */
abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * Dependencies parser
     *
     * @var \Magento\Tools\Dependency\ParserInterface
     */
    protected $dependenciesParser;

    /**
     * Report writer
     *
     * @var \Magento\Tools\Dependency\Report\WriterInterface
     */
    protected $reportWriter;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Builder constructor
     *
     * @param \Magento\Tools\Dependency\ParserInterface $dependenciesParser
     * @param \Magento\Tools\Dependency\Report\WriterInterface $reportWriter
     */
    public function __construct(
        ParserInterface $dependenciesParser,
        WriterInterface $reportWriter
    ) {
        $this->dependenciesParser = $dependenciesParser;
        $this->reportWriter = $reportWriter;
    }

    /**
     * Template method. Main algorithm
     *
     * {@inheritdoc}
     */
    public function build(array $options)
    {
        $this->checkOptions($options);
        $this->options = $options;

        $config = $this->prepareData($this->dependenciesParser->parse($this->options['files_for_parse']));

        $this->reportWriter->write($this->options['report_filename'], $config);
    }

    /**
     * Template method. Check passed options step
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    protected function checkOptions($options)
    {
        if (!isset($options['files_for_parse']) || empty($options['files_for_parse'])) {
            throw new \InvalidArgumentException('Passed option "files_for_parse" is wrong.');
        }

        if (!isset($options['report_filename']) || empty($options['report_filename'])) {
            throw new \InvalidArgumentException('Passed option "report_filename" is wrong.');
        }
    }

    /**
     * Template method. Prepare data for writer step
     *
     * @param array $modulesData
     * @return \Magento\Tools\Dependency\Report\Data\ConfigInterface
     */
    abstract protected function prepareData($modulesData);
}
