<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Scanner;

/**
 * Generate dictionary from phrases
 */
class XmlScanner extends FileScanner
{
    /**
     * {@inheritdoc}
     */
    const FILE_MASK = '/\.xml$/';

    /**
     * {@inheritdoc}
     */
    protected $_defaultPathes = array(
        '/app/code/',
        '/app/design/',
    );

    /**
     * XmlScanner construc
     */
    public function __construct()
    {
        set_error_handler(array($this, "warningHandler"), E_WARNING);
    }

    /**
     * Handle simplexml_load_string warning
     * @return bool
     */
    public function warningHandler()
    {
        return true;
    }

    /**
     * Collect phrases from xml
     * Parse "translate" node and collect phrases:
     * - from itself, it @translate == true
     * - from given attributes, splited by ",", " "
     */
    protected function _collectPhrases()
    {
        foreach ($this->_getFiles() as $file) {
            $xml = new \Magento_Simplexml_Config();
            $xml->loadFile($file, 'SimpleXMLElement');
            $translateNodes = $xml->getXpath("//*[@translate]");
            unset($xml);
            if (!is_array($translateNodes)) {
                continue;
            }
            foreach ($translateNodes as $element) {
                if (!$element instanceof \Magento_Simplexml_Element) {
                    continue;
                }
                $attributes = $element->attributes();
                $attributes = (string)$attributes['translate'];
                if ($attributes == 'true') {
                    $this->_addPhrase((string)$element, $file);
                } else {
                    $nodesDelimeter = strpos($attributes, ' ') === false ? ',' : ' ';
                    $translatedAttributes = explode($nodesDelimeter, $attributes);
                    foreach ($translatedAttributes as $value) {
                        $phrase = (string)$element->$value;
                        if (!$phrase) {
                            continue;
                        }
                        $this->_addPhrase($phrase, $file);
                    }
                }
            }
        }
    }
}
