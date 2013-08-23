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
class JsScanner extends FileScanner
{
    /**
     * {@inheritdoc}
     */
    const FILE_MASK = '/\.(js|phtml)$/';

    /**
     * {@inheritdoc}
     */
    protected $_defaultPathes = array(
        '/app/code/',
        '/app/design/',
        '/pub/lib/mage/',
        '/pub/lib/varien/',
    );

    /**
     * Collect phrases from javascript
     */
    protected function _collectPhrases()
    {
        foreach ($this->_getFiles() as $file) {
            $fileHandle = fopen($file, "r");
            $lineNumber = 0;
            while (!feof($fileHandle)) {
                $lineNumber++;
                $fileRow = fgets($fileHandle, 4096);
                $results = array();
                preg_match_all('/mage\.__\(\s*([\'"])(.*?[^\\\])\1.*?[),]/', $fileRow, $results, PREG_SET_ORDER);
                for ($i = 0; $i < count($results); $i++) {
                    if (isset($results[$i][2])) {
                        $quote = $results[$i][1];
                        $this->_addPhrase($quote . $results[$i][2] . $quote, $file, $lineNumber);
                    }
                }
            }
            fclose($fileHandle);
        }
    }
}
