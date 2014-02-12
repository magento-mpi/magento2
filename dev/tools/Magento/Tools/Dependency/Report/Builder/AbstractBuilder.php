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
 *  Abstract report builder
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

        $config = $this->prepareData($this->dependenciesParser->parse($options['configFiles']));

        $this->reportWriter->write($options['filename'], $config);
    }

    /**
     * Template method. Check passed options step
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    protected function checkOptions($options)
    {
        if (!isset($options['configFiles']) || empty($options['configFiles'])) {
            throw new \InvalidArgumentException('Passed option "configFiles" is wrong.');
        }

        if (!isset($options['filename']) || empty($options['filename'])) {
            throw new \InvalidArgumentException('Passed option "filename" is wrong.');
        }
    }

    /**
     * Template method. Prepare data for writer step
     *
     * @param array $modulesData
     * @return Object
     */
    abstract protected function prepareData($modulesData);
}
