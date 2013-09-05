<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Parser;

/**
 * Parser
 */
class Parser extends AbstractParser
{
    /**
     * Parse one type
     *
     * @param $options
     */
    protected function _parseByTypeOptions($options)
    {
        foreach ($this->_getFiles($options) as $file) {
            $adapter = $this->_adapters[$options['type']];
            $adapter->parse($file);

            foreach ($adapter->getPhrases() as $phrase) {
                $this->_addPhrase($phrase);
            }
        }
    }

    /**
     * Add phrase
     *
     * @param array $phrase
     */
    protected function _addPhrase($phrase)
    {
        $phraseKey = $phrase['phrase'];

        $this->_phrases[$phraseKey] = $this->_factory->createPhrase(array(
            'phrase' => $phrase['phrase'],
            'translation' => $phrase['phrase'],
        ));
    }
}
