<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;
use Magento\Tools\I18n\Code\Dictionary\Scanner\FileScanner;

/**
 * Composite Scanner
 */
class ScannerComposite
{
    /**
     * @var array
     */
    private $_children = array();

    /**
     * @var string
     */
    private $_scanPath;

    /**
     * @var string
     */
    private $_baseDir;

    /**
     * @param string $baseDir
     * @param string $scanPath
     */
    public function __construct($baseDir = '', $scanPath = '')
    {
        $this->_baseDir = $baseDir;
        $this->_scanPath = $scanPath;
    }

    /**
     * Add child scanner
     *
     * @param FileScanner $scanner
     */
    public function addChild(FileScanner $scanner)
    {
        $this->_children[] = $scanner;
    }

    /**
     * Scan files
     *
     * @return array
     */
    public function getPhrases()
    {
        $phrases = array();
        /** @var $scanner FileScanner */
        foreach ($this->_children as $scanner) {
            $scanner->setPath($this->_scanPath);
            $scanner->setBaseDir($this->_baseDir);
            $phrases = array_merge($phrases, $scanner->getPhrases());
        }
        return $phrases;
    }
}
