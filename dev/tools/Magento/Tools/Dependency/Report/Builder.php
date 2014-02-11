<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report;

use Magento\Tools\Dependency\ParserInterface;

/**
 *  Dependencies report builder
 */
class Builder implements BuilderInterface
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
    public function __construct(ParserInterface $dependenciesParser, WriterInterface $reportWriter)
    {
        $this->dependenciesParser = $dependenciesParser;
        $this->reportWriter = $reportWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $options)
    {
        $this->checkOptions($options);

        $config = $this->dependenciesParser->parse($options['configFiles']);

        $this->reportWriter->write($config, $options['filename']);
    }

    /**
     * Check passed options
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
}
