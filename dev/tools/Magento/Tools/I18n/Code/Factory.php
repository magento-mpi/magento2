<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;

use Magento\Tools\I18n\Code\Dictionary;
use Magento\Tools\I18n\Code\Parser;

/**
 *  Abstract Factory
 */
class Factory
{
    /**
     * Create dictionary writer
     *
     * @param string $filename
     * @return \Magento\Tools\I18n\Code\Dictionary\WriterInterface
     * @throws \InvalidArgumentException
     */
    public function createDictionaryWriter($filename = null)
    {
        if (!$filename) {
            $writer = new Dictionary\Writer\Csv\Stdo();
        } else {
            switch (pathinfo($filename, \PATHINFO_EXTENSION)) {
                case 'csv':
                    $writer = new Dictionary\Writer\Csv($filename);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Writer for "%s" is not exist.', $filename));
            }
        }
        return $writer;
    }

    /**
     * Create locale
     *
     * @param string $locale
     * @return \Magento\Tools\I18n\Code\Locale
     */
    public function createLocale($locale)
    {
        return new Locale($locale);
    }

    /**
     * Create dictionary
     *
     * @return \Magento\Tools\I18n\Code\Dictionary
     */
    public function createDictionary()
    {
        return new Dictionary();
    }

    /**
     * Create Phrase
     *
     * @param array $data
     * @return \Magento\Tools\I18n\Code\Dictionary\Phrase
     */
    public function createPhrase(array $data)
    {
        return new Dictionary\Phrase(
            $data['phrase'],
            $data['translation'],
            isset($data['context_type']) ? $data['context_type'] : null,
            isset($data['context_value']) ? $data['context_value'] : null
        );
    }
}
