<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency;

use Magento\File\Csv;
use Magento\Tools\Dependency\Parser;
use Magento\Tools\Dependency\Report\Dependency;
use Magento\Tools\Dependency\Report\Circular;
use Magento\TestFramework\Dependency\Circular as CircularTool;

/**
 *  Service Locator (instead DI container)
 */
class ServiceLocator
{
    /**
     * Xml dependencies parser
     *
     * @var \Magento\Tools\Dependency\ParserInterface
     */
    private static $configDependenciesParser;

    /**
     * Dependencies report builder
     *
     * @var \Magento\Tools\Dependency\Report\BuilderInterface
     */
    private static $dependenciesReportBuilder;

    /**
     * Circular dependencies report builder
     *
     * @var \Magento\Tools\Dependency\Report\BuilderInterface
     */
    private static $circularDependenciesReportBuilder;

    /**
     * Get modules dependencies report builder
     *
     * @return \Magento\Tools\Dependency\Report\BuilderInterface
     */
    public static function getDependenciesReportBuilder()
    {
        if (null === self::$dependenciesReportBuilder) {
            self::$dependenciesReportBuilder = new Dependency\Builder(
                self::getConfigDependenciesParser(),
                new Dependency\Writer((new Csv())->setDelimiter(';'))
            );
        }
        return self::$dependenciesReportBuilder;
    }

    /**
     * Get modules circular dependencies report builder
     *
     * @return \Magento\Tools\Dependency\Report\BuilderInterface
     */
    public static function getCircularDependenciesReportBuilder()
    {
        if (null === self::$circularDependenciesReportBuilder) {
            self::$circularDependenciesReportBuilder = new Circular\Builder(
                self::getConfigDependenciesParser(),
                new Circular\Writer((new Csv())->setDelimiter(';')),
                new CircularTool(array(), null)
            );
        }
        return self::$circularDependenciesReportBuilder;
    }

    /**
     * Get framework dependencies report builder
     *
     * @return \Magento\Tools\Dependency\Report\BuilderInterface
     */
    public static function getFrameworkDependenciesReportBuilder()
    {
    }

    /**
     * Get dependencies parser from config files
     *
     * @return \Magento\Tools\Dependency\ParserInterface
     */
    private static function getConfigDependenciesParser()
    {
        if (null === self::$configDependenciesParser) {
            self::$configDependenciesParser = new Parser\Config();
        }
        return self::$configDependenciesParser;
    }
}
