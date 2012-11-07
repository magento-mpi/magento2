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
    protected $_words = array();

    /**
     * Map of whitelisted paths to whitelisted words
     *
     * @var array
     */
    protected $_whitelist = array();

    /**
     * Path to base dir, used to calculate relative paths
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * @param string|array $configFiles
     * @param string $baseDir
     * @throws Inspection_Exception
     */
    public function __construct($configFiles, $baseDir)
    {
        if (!is_dir($baseDir)) {
            throw new Inspection_Exception("Base directory {$baseDir} does not exist");
        }
        $this->_baseDir = realpath($baseDir);

        if (!is_array($configFiles)) {
            $configFiles = array($configFiles);
        }
        foreach ($configFiles as $configFile) {
            $this->_loadConfig($configFile);
        }
    }

    /**
     * Load configuration from file, adding words and whitelisted entries to main config
     *
     * @param $configFile
     * @throws Inspection_Exception
     */
    protected function _loadConfig($configFile)
    {
        if (!file_exists($configFile)) {
            throw new Inspection_Exception("Configuration file {$configFile} does not exist");
        }
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
        $words = array_filter($words);

        $words = array_merge($this->_words, $words);
        $this->_words = array_unique($words);
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
        // Load whitelist entries
        $whitelist = array();
        $nodes = $configXml->xpath('//config/whitelist/item');
        foreach ($nodes as $node) {
            $path = $node->xpath('path');
            if (!$path) {
                throw new Inspection_Exception('Wrong whitelisted path configuration');
            }
            $path = (string) $path[0];

            // Words
            $words = array();
            $wordNodes = $node->xpath('word');
            if ($wordNodes) {
                foreach ($wordNodes as $wordNode) {
                    $words[] = (string) $wordNode;
                }
            }

            $whitelist[$path] = $words;
        }

        // Merge with already present whitelist
        foreach ($whitelist as $newPath => $newWords) {
            if (isset($this->_whitelist[$newPath])) {
                $newWords = array_merge($this->_whitelist[$newPath], $newWords);
            }
            $this->_whitelist[$newPath] = array_unique($newWords);
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
        foreach ($this->_whitelist as $whitelistPath => $whitelistWords) {
            if (strncmp($whitelistPath, $path, strlen($whitelistPath)) != 0) {
                continue;
            }

            if (!$whitelistWords) { // All words are permitted there
                return array();
            }
            $foundWords = array_diff($foundWords, $whitelistWords);
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
