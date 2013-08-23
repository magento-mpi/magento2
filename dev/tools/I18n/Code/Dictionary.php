<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;

/**
 * Generate dictionary from phrases
 */
class Dictionary
{
    /**
     * @var string
     */
    private $_outputFilename = null;

    /**
     * @var resource
     */
    private $_outputFileHandler;

    /**
     * @var bool
     */
    private $_withContext = true;

    /**
     * @var Dictionary\ScannerComposite
     */
    private $_scanner;

    /**
     * @param Dictionary\ScannerComposite $phraseCollector
     */
    public function __construct(Dictionary\ScannerComposite $phraseCollector)
    {
        $this->_scanner = $phraseCollector;
    }

    /**
     * @param bool $useContext
     */
    public function setWithContext($useContext)
    {
        $this->_withContext = $useContext;
    }

    /**
     * @param string|null $outputFilename
     */
    public function setOutputFilename($outputFilename)
    {
        $this->_outputFilename = $outputFilename;
    }

    /**
     * Get output filename
     *
     * @return null|string
     */
    private function _getOutputFilename()
    {
        return $this->_outputFilename;
    }

    /**
     * Generate dictionary
     */
    public function generate()
    {
        $this->_initRecord();
        $this->_saveDicntionary();
        $this->_endRecord();
    }

    /**
     * Init dictionary record
     * @throws \Exception
     */
    protected function _initRecord()
    {
        $this->_outputFileHandler = $this->_getOutputFilename() ? fopen($this->_getOutputFilename(), 'w') : STDOUT;
        if (false === $this->_outputFileHandler) {
            throw new \Exception(sprintf('Cannot open file for write dictionary: "%s"', $this->_getOutputFilename()));
        }
    }

    /**
     * Save generated dictinary
     */
    protected function _saveDicntionary()
    {
        foreach ($this->_scanner->getPhrases() as $phrases) {
            $this->_savePhrase($phrases);
        }
    }

    /**
     * @param array $phrase
     */
    private function _savePhrase($phrase)
    {
        $fields = array($phrase['phrase'], $phrase['phrase']);
        if ($this->_withContext) {
            $fields[] = $phrase['context_type'];
            $fields[] = implode(',', array_keys($phrase['context']));
        }
        fputcsv($this->_outputFileHandler, $fields, ',', '"');
    }

    /**
     * Init dictionary record
     */
    private function _endRecord()
    {
        fclose($this->_outputFileHandler);
    }
}
