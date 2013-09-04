<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;

use Magento\Tools\I18n\Code\Parser;
use Magento\Tools\I18n\Code\Dictionary;
use Magento\Tools\I18n\Code\Pack;

/**
 *  Service Locator (instead DI container)
 */
class ServiceLocator
{
    /**
     * Domain abstract factory
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    private static $_factory;

    /**
     * Context manager
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    private static $_context;

    /**
     * Dictionary generator
     *
     * @var \Magento\Tools\I18n\Code\Dictionary\Generator
     */
    private static $_dictionaryGenerator;

    /**
     * Pack generator
     *
     * @var \Magento\Tools\I18n\Code\Pack\Generator
     */
    private static $_packGenerator;

    /**
     * Get factory
     *
     * @return \Magento\Tools\I18n\Code\Factory
     */
    public static function getFactory()
    {
        if (null === self::$_factory) {
            $parserFactory = new Parser\Factory(self::getContext());

            self::$_factory = new Factory($parserFactory);
        }
        return self::$_factory;
    }

    /**
     * Get context
     *
     * @return \Magento\Tools\I18n\Code\Context
     */
    public static function getContext()
    {
        if (null === self::$_context) {
            self::$_context = new Context();
        }
        return self::$_context;
    }

    /**
     * Get dictionary generator
     *
     * @return \Magento\Tools\I18n\Code\Dictionary\Generator
     */
    public static function getDictionaryGenerator()
    {
        if (null === self::$_dictionaryGenerator) {
            self::$_dictionaryGenerator = new Dictionary\Generator(self::getFactory());
        }
        return self::$_dictionaryGenerator;
    }

    /**
     * Get pack generator
     *
     * @return \Magento\Tools\I18n\Code\Pack\Generator
     */
    public static function getPackGenerator()
    {
        if (null === self::$_packGenerator) {
            $dictionaryLoader = new Dictionary\Loader\File\Csv(self::getFactory());
            $packWriter = new Pack\Writer\File\Csv(self::getContext(), $dictionaryLoader, self::getFactory());

            self::$_packGenerator = new Pack\Generator($dictionaryLoader, $packWriter, self::getFactory());
        }
        return self::$_packGenerator;
    }
}
