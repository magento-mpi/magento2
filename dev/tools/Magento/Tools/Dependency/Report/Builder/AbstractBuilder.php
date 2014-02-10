<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Builder;

use Magento\Tools\Dependency\ParserInterface;
use Magento\Tools\Dependency\Report\BuilderInterface;
use Magento\Tools\Dependency\Report\WriterInterface;

/**
 *  Abstract dependencies report builder
 */
abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var \Magento\Tools\Dependency\ParserInterface
     */
    protected $dependenciesParser;

    /**
     * @var \Magento\Tools\Dependency\Report\WriterInterface
     */
    protected $reportWriter;

    /**
     * Builder constructor
     *
     * @param \Magento\Tools\Dependency\ParserInterface $dependenciesParser
     * @param \Magento\Tools\Dependency\Report\WriterInterface $reportWriter
     */
    public function __construct(ParserInterface $dependenciesParser, WriterInterface $reportWriter)
    {
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
        $dependencies = $this->dependenciesParser->parse($options['moduleConfigs']);
        $reportData = $this->buildReportData($dependencies);

        $this->reportWriter->write($reportData, $options['filename']);
    }

    /**
     * Template method. Step of main algorithm
     *
     * @param array $dependencies
     * @return array
     */
    abstract protected function buildReportData($dependencies);
}
