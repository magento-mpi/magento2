<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Parser;

use Magento\Tools\I18n\Code;

/**
 * Contextual Parser
 */
class Contextual extends AbstractParser
{
    /**
     * Context
     *
     * @var \Magento\Tools\I18n\Code\Context
     */
    protected $_context;

    /**
     * Parser construct
     *
     * @param \Magento\Tools\I18n\Code\FilesCollector $filesCollector
     * @param \Magento\Tools\I18n\Code\Factory $factory
     * @param \Magento\Tools\I18n\Code\Context $context
     */
    public function __construct(Code\FilesCollector $filesCollector, Code\Factory $factory, Code\Context $context)
    {
        $this->_context = $context;

        parent::__construct($filesCollector, $factory);
    }

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

            list($contextType, $contextValue) = $this->_context->getContextByPath($file);

            foreach ($adapter->getPhrases() as $phraseData) {
                $this->_addPhrase($phraseData, $contextType, $contextValue);
            }
        }
    }

    /**
     * Add phrase with context
     *
     * @param array $phraseData
     * @param string $contextType
     * @param string $contextValue
     */
    protected function _addPhrase($phraseData, $contextType, $contextValue)
    {
        $phraseKey = $contextType . $phraseData['phrase'];

        if (isset($this->_phrases[$phraseKey])) {
            /** @var \Magento\Tools\I18n\Code\Dictionary\Phrase $phrase */
            $phrase = $this->_phrases[$phraseKey];
            $phrase->addContextValue($contextValue);
        } else {
            $this->_phrases[$phraseKey] = $this->_factory->createPhrase(array(
                'phrase' => $phraseData['phrase'],
                'translation' => $phraseData['phrase'],
                'context_type' => $contextType,
                'context_value' => array($contextValue),
            ));
        }
    }
}
