<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;

use Magento\Tools\I18n\Code\Dictionary\Phrase;

/**
 *  Dictionary
 */
class Dictionary
{
    /**
     * Phrases
     *
     * @var array
     */
    private $_phrases = array();

    /**
     * List of phrases where array key is vo key
     *
     * @var array
     */
    private $_phrasesByKey = array();

    /**
     * Add phrase to pack container
     *
     * @param \Magento\Tools\I18n\Code\Dictionary\Phrase $phrase
     */
    public function addPhrase(Phrase $phrase)
    {
        $this->_phrases[] = $phrase;
        $this->_phrasesByKey[$phrase->getKey()][] = $phrase;
    }

    /**
     * Get phrases
     *
     * @return \Magento\Tools\I18n\Code\Dictionary\Phrase[]
     */
    public function getPhrases()
    {
        return $this->_phrases;
    }

    /**
     * Get duplicates in container
     *
     * @return array
     */
    public function getDuplicates()
    {
        return array_filter($this->_phrasesByKey, function ($phrases) {
            return count($phrases) > 1;
        });
    }
}
