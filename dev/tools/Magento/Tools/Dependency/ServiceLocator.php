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
use Magento\Tools\Dependency\Report\Framework;
use Magento\Tools\Dependency\Circular as CircularTool;
use Magento\TestFramework\Utility\Files;

/**
 *  Service Locator (instead DI container)
 */
class ServiceLocator
{
    /**
     * Xml config dependencies parser
     *
     * @var \Magento\Tools\Dependency\ParserInterface
     */
    private static $xmlConfigParser;

    /**
     * Framework dependencies parser
     *
     * @var \Magento\Tools\Dependency\ParserInterface
     */
    private static $frameworkDependenciesParser;

    /**
     * Modules dependencies report builder
     *
     * @var \Magento\Tools\Dependency\Report\BuilderInterface
     */
    private static $dependenciesReportBuilder;

    /**
     * Modules circular dependencies report builder
     *
     * @var \Magento\Tools\Dependency\Report\BuilderInterface
     */
    private static $circularDependenciesReportBuilder;

    /**
     * Framework dependencies report builder
     *
     * @var \Magento\Tools\Dependency\Report\BuilderInterface
     */
    private static $frameworkDependenciesReportBuilder;

    /**
     * Csv file tool
     *
     * @var \Magento\File\Csv
     */
    private static $csvTool;

    /**
     * Get modules dependencies report builder
     *
     * @return \Magento\Tools\Dependency\Report\BuilderInterface
     */
    public static function getDependenciesReportBuilder()
    {
        if (null === self::$dependenciesReportBuilder) {
            self::$dependenciesReportBuilder = new Dependency\Builder(
                self::getXmlConfigParser(),
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
                self::getXmlConfigParser(),
                new Circular\Writer(self::getCsvTool()),
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
        if (null === self::$frameworkDependenciesReportBuilder) {
            self::$frameworkDependenciesReportBuilder = new Framework\Builder(
                self::getFrameworkDependenciesParser(),
                new Framework\Writer(self::getCsvTool()),
                self::getXmlConfigParser()
            );
        }
        return self::$frameworkDependenciesReportBuilder;
    }

    /**
     * Get modules dependencies parser
     *
     * @return \Magento\Tools\Dependency\ParserInterface
     */
    private static function getXmlConfigParser()
    {
        if (null === self::$xmlConfigParser) {
            self::$xmlConfigParser = new Parser\Config\Xml();
        }
        return self::$xmlConfigParser;
    }

    /**
     * Get framework dependencies parser
     *
     * @return \Magento\Tools\Dependency\ParserInterface
     */
    private static function getFrameworkDependenciesParser()
    {
        if (null === self::$frameworkDependenciesParser) {
            self::$frameworkDependenciesParser = new Parser\Code(
                Files::init()->getNamespaces()
            );
        }
        return self::$frameworkDependenciesParser;
    }

    /**
     * Get csv file tool
     *
     * @return \Magento\File\Csv
     */
    private static function getCsvTool()
    {
        if (null === self::$csvTool) {
            self::$csvTool = (new Csv())->setDelimiter(';');
        }
        return self::$csvTool;
    }
}
