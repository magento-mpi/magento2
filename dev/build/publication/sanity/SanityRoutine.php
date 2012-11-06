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
 * Routine with run-time functions
 */
class SanityRoutine
{
    /**
     * Tool class, used to load config and check files
     *
     * @var Inspection_Sanity
     */
    protected $_sanityChecker;

    /**
     * Base directory, used to start searching from
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * @param string $configFile
     * @param string $baseDir
     */
    public function __construct($configFile, $baseDir)
    {
        $this->_sanityChecker = new Inspection_Sanity($configFile, $baseDir);
        $this->_baseDir = realpath($baseDir);
    }

    /**
     * Get list of words, configured to be searched
     *
     * @return array
     */
    public function getWords()
    {
        return $this->_sanityChecker->getWords();
    }

    /**
     * Searches words in files content within base directory tree
     *
     * @return array
     */
    public function findWords()
    {
        return $this->_findWords($this->_baseDir);
    }

    /**
     * Searches words in files content within directory tree
     *
     * @param  string $currentDir Current dir to look in
     * @return array
     */
    protected function _findWords($currentDir)
    {
        $result = array();

        $entries = glob($currentDir . DIRECTORY_SEPARATOR . '*');
        $initialLength = strlen($this->_baseDir);
        foreach ($entries as $entry) {
            if (is_file($entry)) {
                $foundWords = $this->_sanityChecker->findWords($entry);
                if (!$foundWords) {
                    continue;
                }
                $relPath = substr($entry, $initialLength + 1);
                $result[] = array('words' => $foundWords, 'file' => $relPath);
            } else if (is_dir($entry)) {
                $more = $this->_findWords($entry);
                $result = array_merge($result, $more);
            }
        }

        return $result;
    }
}
