<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

/**
 *  Phrase
 */
class Phrase
{
    /**
     * Phrase
     *
     * @var string
     */
    private $_phrase;

    /**
     * Translation
     *
     * @var string
     */
    private $_translation;

    /**
     * Context type
     *
     * @var string
     */
    private $_contextType;

    /**
     * Context value
     *
     * @var array
     */
    private $_contextValue;

    /**
     * Line
     *
     * @var int
     */
    private $_line;

    /**
     * Phrase construct
     *
     * @param string $phrase
     * @param string $translation
     * @param string $contextType
     * @param string|array $contextValue
     * @param int $line
     * @throws \DomainException
     */
    public function __construct($phrase, $translation, $contextType, $contextValue, $line)
    {
        if (!$phrase) {
            throw new \DomainException('Missed phrase.');
        }
        $this->_phrase = $phrase;

        if (!$translation) {
            throw new \DomainException('Missed translation.');
        }
        $this->_translation = $translation;

        $this->_contextType = $contextType;
        $this->_contextValue = is_string($contextValue) ? explode(',', $contextValue) : $contextValue;
        $this->_line = $line;
    }

    /**
     * Get phrase
     *
     * @return string
     */
    public function getPhrase()
    {
        return $this->_phrase;
    }

    /**
     * Get translation
     *
     * @return string
     */
    public function getTranslation()
    {
        return $this->_translation;
    }

    /**
     * Get context type
     *
     * @return string
     */
    public function getContextType()
    {
        return $this->_contextType;
    }

    /**
     * Get context value
     *
     * @return array
     */
    public function getContextValue()
    {
        return $this->_contextValue;
    }

    /**
     * Get line
     *
     * @return array
     */
    public function getLine()
    {
        return $this->_line;
    }

    /**
     * Get VO identifier key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getPhrase() . $this->getContextType();
    }
}
