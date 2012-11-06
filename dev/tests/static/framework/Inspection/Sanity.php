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
     * @var array
     */
    protected $_config;

    /**
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

        $config = array(
            'words' => array(),
            'whitelist' => array()
        );

        try {
            $xml = new SimpleXMLElement(file_get_contents($configFile));
        } catch (Exception $e) {
            throw new Inspection_Exception($e->getMessage(), $e->getCode(), $e);
        }

        // Load words
        $words = array();
        $nodes = $xml->xpath('//config/words/word');
        foreach ($nodes as $node) {
            $words[] = (string) $node;
        }
        $config['words'] = array_filter($words);
        if (!$config['words']) {
            throw new Inspection_Exception('No words to check');
        }

        // Load whitelisted entries
        $nodes = $xml->xpath('//config/whitelist/item');
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

            $config['whitelist'][] = $entry;
        }

        $this->_config = $config;
        $this->_baseDir = realpath($baseDir);
    }

    /**
     * Get list of words, configured to be searched
     *
     * @return array
     */
    public function getWords()
    {
        return $this->_config['words'];
    }

    /**
     * Checks the file content against the list of words
     *
     * @param  string $file
     * @return array Words, found
     */
    public function findWords($file)
    {
        $foundWords = $this->_findWords($file);
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
     * @return array
     */
    protected function _findWords($file)
    {
        $contents = file_get_contents($file);

        $foundWords = array();
        foreach ($this->_config['words'] as $word) {
            if (stripos($contents, $word) !== false) {
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
        foreach ($this->_config['whitelist'] as $item) {
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
}
