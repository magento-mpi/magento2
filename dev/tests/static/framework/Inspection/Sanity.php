<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sanity checking routine
 */
class Inspection_Sanity
{
    /**
     * Words to search for
     *
     * @var array
     */
    protected $_words;

    /**
     * Whitelist with paths and words
     *
     * @var array
     */
    protected $_whitelist;

    /**
     * Path to base dir, used to calculate relative paths
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * @param string $configFile
     * @param string $baseDir
     * @throws Inspection_Exception
     */
    public function __construct($configFile, $baseDir)
    {
        if (!file_exists($configFile)) {
            throw new Inspection_Exception("Configuration file {$configFile} does not exist");
        }
        if (!is_dir($baseDir)) {
            throw new Inspection_Exception("Base directory {$baseDir} does not exist");
        }
        $this->_baseDir = realpath($baseDir);

        try {
            $xml = new SimpleXMLElement(file_get_contents($configFile));
        } catch (Exception $e) {
            throw new Inspection_Exception($e->getMessage(), $e->getCode(), $e);
        }

        $this->_extractWords($xml)
            ->_extractWhitelist($xml);
    }

    /**
     * Extract words from configuration xml
     *
     * @param SimpleXMLElement $configXml
     * @return Inspection_Sanity
     * @throws Inspection_Exception
     */
    protected function _extractWords(SimpleXMLElement $configXml)
    {
        $words = array();
        $nodes = $configXml->xpath('//config/words/word');
        foreach ($nodes as $node) {
            $words[] = (string) $node;
        }
        $config['words'] = array_filter($words);
        if (!$config['words']) {
            throw new Inspection_Exception('No words to check');
        }

        $this->_words = $words;
        return $this;
    }

    /**
     * Extract whitelisted entries and words from configuration xml
     *
     * @param SimpleXMLElement $configXml
     * @return Inspection_Sanity
     * @throws Inspection_Exception
     */
    protected function _extractWhitelist(SimpleXMLElement $configXml)
    {
        $nodes = $configXml->xpath('//config/whitelist/item');
        foreach ($nodes as $node) {
            $entry = array();

            $path = $node->xpath('path');
            if (!$path) {
                throw new Inspection_Exception('Wrong whitelisted path configuration');
            }
            $entry['path'] = (string) $path[0];

            // Words
            $wordNodes = $node->xpath('word');
            if ($wordNodes) {
                $entry['words'] = array();
                foreach ($wordNodes as $wordNode) {
                    $word = (string) $wordNode;
                    $entry['words'][] = $word;
                }
            }

            $this->_whitelist[] = $entry;
        }
        return $this;
    }


    /**
     * Get list of words, configured to be searched
     *
     * @return array
     */
    public function getWords()
    {
        return $this->_words;
    }

    /**
     * Checks the file content against the list of words
     *
     * @param  string $file
     * @param  bool $checkContents
     * @return array Words, found
     */
    public function findWords($file, $checkContents = true)
    {
        $foundWords = $this->_findWords($file, $checkContents);
        if (!$foundWords) {
            return array();
        }

        $relPath = substr($file, strlen($this->_baseDir) + 1);
        $foundWords = self::_removeWhitelistedWords($relPath, $foundWords);
        if (!$foundWords) {
            return array();
        }

        return $foundWords;
    }

    /**
     * Tries to find specific words in the file
     *
     * @param  string $file
     * @param  bool $checkContents
     * @return array
     */
    protected function _findWords($file, $checkContents = true)
    {
        $relPath = $this->_getRelPath($file);
        $contents = $checkContents ? file_get_contents($file) : '';

        $foundWords = array();
        foreach ($this->_words as $word) {
            if ((stripos($contents, $word) !== false) || (stripos($relPath, $word) !== false)) {
                $foundWords[] = $word;
            }
        }
        return $foundWords;
    }

    /**
     * Removes whitelisted words from array of found words
     *
     * @param  string $path
     * @param  array $foundWords
     * @return array
     */
    protected function _removeWhitelistedWords($path, $foundWords)
    {
        $path = str_replace('\\', '/', $path);
        foreach ($this->_whitelist as $item) {
            if (strncmp($item['path'], $path, strlen($item['path'])) != 0) {
                continue;
            }

            if (!isset($item['words'])) { // All words are permitted there
                return array();
            }
            $foundWords = array_diff($foundWords, $item['words']);
        }
        return $foundWords;
    }

    /**
     * Return file path relative to base dir
     *
     * @param string $file
     * @return string
     */
    protected function _getRelPath($file)
    {
        return substr($file, strlen($this->_baseDir) + 1);
    }
}
