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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * Get dictionary generator
     *
     * @return \Magento\Tools\I18n\Code\Dictionary\Generator
     */
    public static function getDictionaryGenerator()
    {
        if (null === self::$_dictionaryGenerator) {
            $filesCollector = new FilesCollector();

            $phraseCollector = new Parser\Adapter\Php\Tokenizer\PhraseCollector(new Parser\Adapter\Php\Tokenizer());
            $adapters = array(
                'php' => new Parser\Adapter\Php($phraseCollector),
                'js' => new Parser\Adapter\Js(),
                'xml' => new Parser\Adapter\Xml(),
            );

            $parser = new Parser\Parser($filesCollector, self::_getFactory());
            $parserContextual = new Parser\Contextual($filesCollector, self::_getFactory(), self::_getContext());
            foreach ($adapters as $type => $adapter) {
                $parser->addAdapter($type, $adapter);
                $parserContextual->addAdapter($type, $adapter);
            }

            self::$_dictionaryGenerator = new Dictionary\Generator($parser, $parserContextual, self::_getFactory());
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
            $dictionaryLoader = new Dictionary\Loader\File\Csv(self::_getFactory());
            $packWriter = new Pack\Writer\File\Csv(self::_getContext(), $dictionaryLoader, self::_getFactory());

            self::$_packGenerator = new Pack\Generator($dictionaryLoader, $packWriter, self::_getFactory());
        }
        return self::$_packGenerator;
    }

    /**
     * Get factory
     *
     * @return \Magento\Tools\I18n\Code\Factory
     */
    private static function _getFactory()
    {
        if (null === self::$_factory) {
            self::$_factory = new Factory();
        }
        return self::$_factory;
    }

    /**
     * Get context
     *
     * @return \Magento\Tools\I18n\Code\Context
     */
    private static function _getContext()
    {
        if (null === self::$_context) {
            self::$_context = new Context();
        }
        return self::$_context;
    }
}
