<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Framework;

use Magento\Tools\Dependency\ParserInterface;
use Magento\Tools\Dependency\Report\Builder\AbstractBuilder;
use Magento\Tools\Dependency\Report\WriterInterface;

/**
 *  Framework dependencies report builder
 */
class Builder extends AbstractBuilder
{
    /**
     * Confug parser
     *
     * @var \Magento\Tools\Dependency\ParserInterface
     */
    protected $configParser;

    /**
     * Builder constructor
     *
     * @param \Magento\Tools\Dependency\ParserInterface $dependenciesParser
     * @param \Magento\Tools\Dependency\Report\WriterInterface $reportWriter
     * @param \Magento\Tools\Dependency\ParserInterface $configParser
     */
    public function __construct(
        ParserInterface $dependenciesParser,
        WriterInterface $reportWriter,
        ParserInterface $configParser
    ) {
        parent::__construct($dependenciesParser, $reportWriter);

        $this->configParser = $configParser;
    }

    /**
     * Template method. Check passed options step
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    protected function checkOptions($options)
    {
        parent::checkOptions($options);

        if (!isset($options['config_files']) || empty($options['config_files'])) {
            throw new \InvalidArgumentException('Passed option "config_files" is wrong.');
        }
    }

    /**
     * Template method. Prepare data for writer step
     *
     * @param array $modulesData
     * @return \Magento\Tools\Dependency\Report\Framework\Data\Config
     */
    protected function prepareData($modulesData)
    {
        $allowedModules = $this->getAllowedModules();

        $modules = [];
        foreach ($modulesData as $moduleData) {
            $dependencies = [];
            foreach ($moduleData['dependencies'] as $dependencyData) {
                if (!in_array($dependencyData['lib'], $allowedModules)) {
                    $dependencies[] = new Data\Dependency($dependencyData['lib'], $dependencyData['count']);
                }
            }
            $modules[] = new Data\Module($moduleData['name'], $dependencies);
        }
        return new Data\Config($modules);
    }

    /**
     * Get allowed modules
     *
     * @return array
     */
    protected function getAllowedModules()
    {
        return array_map(function ($element) {
            return $element['name'];
        }, $this->configParser->parse($this->options['config_files']));
    }
}
