<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency;

use Magento\Tools\Dependency\Report\Builder;
use Magento\Tools\Dependency\Parser;
use Magento\Tools\Dependency\Report\Writer;

/**
 *  Service Locator (instead DI container)
 */
class ServiceLocator
{
    /**
     * @var \Magento\Tools\Dependency\Parser\Xml
     */
    private static $xmlDependenciesParser;

    /**
     * Dictionary generator
     *
     * @var \Magento\Tools\Dependency\Report\Builder
     */
    private static $modulesDependenciesReportBuilder;

    /**
     * Get modules dependencies report builder
     *
     * @return \Magento\Tools\Dependency\Report\Builder
     */
    public static function getModulesDependenciesReportBuilder()
    {
        if (null === self::$modulesDependenciesReportBuilder) {
            self::$modulesDependenciesReportBuilder = new Builder(
                self::getXmlDependenciesParser(),
                new Writer\Csv\Module()
            );
        }
        return self::$modulesDependenciesReportBuilder;
    }

    /**
     * Get modules circular dependencies report builder
     *
     * @return \Magento\Tools\Dependency\Report\BuilderInterface
     */
    public static function getModulesCircularDependenciesReportBuilder()
    {
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
     * Get dependencies parser from xml files
     *
     * @return \Magento\Tools\Dependency\Parser\Xml
     */
    private static function getXmlDependenciesParser()
    {
        if (null === self::$xmlDependenciesParser) {
            self::$xmlDependenciesParser = new Parser\Xml();
        }
        return self::$xmlDependenciesParser;
    }
}
