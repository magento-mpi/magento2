<?php
/**
 * Service routines for sanity check command line script
 *
 * {license_notice}
 *
 * @category   build
 * @package    sanity
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Extend words finder class, which is designed for sanity tests. The added functionality is method to search through
 * directories and method to return words list for logging.
 */
class SanityWordsFinder extends Magento_TestFramework_Inspection_WordsFinder
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
     * Searche words in files content recursively within base directory tree
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
        $result = array();

        $entries = glob($currentDir . DIRECTORY_SEPARATOR . '*');
        $initialLength = strlen($this->_baseDir);
        foreach ($entries as $entry) {
            if (is_file($entry)) {
                $foundWords = $this->findWords($entry);
                if (!$foundWords) {
                    continue;
                }
                $relPath = substr($entry, $initialLength + 1);
                $result[] = array('words' => $foundWords, 'file' => $relPath);
            } else if (is_dir($entry)) {
                $more = $this->_findWordsRecursively($entry);
                $result = array_merge($result, $more);
            }
        }

        return $result;
    }
}
