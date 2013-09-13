<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Pack;

use Magento\Tools\I18n\Code\Dictionary;
use Magento\Tools\I18n\Code\Pack;
use Magento\Tools\I18n\Code\Factory;

/**
 * Pack generator
 */
class Generator
{
    /**
     * Dictionary loader
     *
     * @var \Magento\Tools\I18n\Code\Dictionary\Loader\FileInterface
     */
    protected $_dictionaryLoader;

    /**
     * Pack writer
     *
     * @var \Magento\Tools\I18n\Code\Pack\WriterInterface
     */
    protected $_packWriter;

    /**
     * Domain abstract factory
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $_factory;

    /**
     * Loader construct
     *
     * @param \Magento\Tools\I18n\Code\Dictionary\Loader\FileInterface $dictionaryLoader
     * @param \Magento\Tools\I18n\Code\Pack\WriterInterface $packWriter
     * @param \Magento\Tools\I18n\Code\Factory $factory
     */
    public function __construct(
        Dictionary\Loader\FileInterface $dictionaryLoader,
        Pack\WriterInterface $packWriter,
        Factory $factory
    ) {
        $this->_dictionaryLoader = $dictionaryLoader;
        $this->_packWriter = $packWriter;
        $this->_factory = $factory;
    }

    /**
     * Generate language pack
     *
     * @param string $dictionaryPath
     * @param string $packPath
     * @param string $locale
     * @param string $mode One of const of WriterInterface::MODE_
     * @param bool $allowDuplicates
     * @throws \RuntimeException
     */
    public function generate($dictionaryPath, $packPath, $locale, $mode = WriterInterface::MODE_REPLACE,
        $allowDuplicates = false
    ) {
        $locale = $this->_factory->createLocale($locale);
        $dictionary = $this->_dictionaryLoader->load($dictionaryPath);

        if (!$allowDuplicates && ($duplicates = $dictionary->getDuplicates())) {
            throw new \RuntimeException($this->_createDuplicatesPhrasesError($duplicates));
        }

        $this->_packWriter->write($dictionary, $packPath, $locale, $mode);
    }

    /**
     * Get duplicates error
     *
     * @param array $duplicates
     * @return string
     */
    protected function _createDuplicatesPhrasesError($duplicates)
    {
        $error = '';
        foreach ($duplicates as $phrases) {
            /** @var \Magento\Tools\I18n\Code\Dictionary\Phrase $phrase */
            $phrase = $phrases[0];
            $error .= sprintf("Error. The phrase \"%s\" is translated differently in %d places.\n",
                $phrase->getPhrase(), count($phrases));
        }
        return $error;
    }
}
