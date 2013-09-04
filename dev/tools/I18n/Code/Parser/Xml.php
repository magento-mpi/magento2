<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Parser;

/**
 * Xml data parser
 *
 * Parse "translate" node and collect phrases:
 * - from itself, it @translate == true
 * - from given attributes, split by ",", " "
 */
class Xml extends AbstractParser
{
    /**
     * {@inheritdoc}
     */
    protected function _parse($file)
    {
        $this->_setErrorHandler();
        foreach ($this->_getNodes($file) as $element) {
            if (!$element instanceof \Magento_Simplexml_Element) {
                continue;
            }
            $attributes = $element->attributes();
            if ((string)$attributes['translate'] == 'true') {
                $this->_addPhrase((string)$element, $file);
            } else {
                $nodesDelimiter = strpos($attributes, ' ') === false ? ',' : ' ';
                foreach (explode($nodesDelimiter, $attributes) as $value) {
                    $phrase = (string)$element->$value;
                    if ($phrase) {
                        $this->_addPhrase($phrase, $file);
                    }
                }
            }
        }
        $this->_restoreErrorHandler();
    }

    /**
     * Set error handler
     */
    protected function _setErrorHandler()
    {
        set_error_handler(function () {
            return true;
        }, \E_WARNING);
    }

    /**
     * Restore error handler
     */
    protected function _restoreErrorHandler()
    {
        restore_error_handler();
    }

    /**
     * Get nodes with translation
     *
     * @param string $file
     * @return array
     */
    protected function _getNodes($file)
    {
        $xml = new \Magento_Simplexml_Config();
        $xml->loadFile($file, 'SimpleXMLElement');
        $nodes = $xml->getXpath("//*[@translate]");
        unset($xml);
        return is_array($nodes) ? $nodes : array();
    }
}
