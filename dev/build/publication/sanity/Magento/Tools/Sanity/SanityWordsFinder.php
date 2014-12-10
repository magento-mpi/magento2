<?php
/**
 * Service routines for sanity check command line script
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Sanity;

/**
 * Extend words finder class, which is designed for sanity tests. The added functionality is method to search through
 * directories and method to return words list for logging.
 */
class SanityWordsFinder extends \Magento\TestFramework\Inspection\WordsFinder
{
    /**
     * Get list of words, configured to be searched
     *
     * @return array
     */
    public function getSearchedWords()
    {
        return $this->_words;
    }

    /**
     * Search words in files content recursively within base directory tree
     *
     * @return array
     */
    public function findWordsRecursively()
    {
        return $this->_findWordsRecursively($this->_baseDir);
    }

    /**
     * Search words in files content recursively within base directory tree
     *
     * @param  string $currentDir Current dir to look in
     * @return array
     */
    protected function _findWordsRecursively($currentDir)
    {
        $result = [];

        $entries = glob($currentDir . '/*');
        $initialLength = strlen($this->_baseDir);
        foreach ($entries as $entry) {
            if (is_file($entry)) {
                $foundWords = $this->findWords($entry);
                if (!$foundWords) {
                    continue;
                }
                $relPath = substr($entry, $initialLength + 1);
                $result[] = ['words' => $foundWords, 'file' => $relPath];
            } elseif (is_dir($entry)) {
                $more = $this->_findWordsRecursively($entry);
                $result = array_merge($result, $more);
            }
        }

        return $result;
    }
}
