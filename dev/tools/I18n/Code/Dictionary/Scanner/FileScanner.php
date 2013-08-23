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
abstract class FileScanner
{
    /**#@+
     * Phrase context information
     */
    const CONTEXT_TYPE_MODULE = 'module';
    const CONTEXT_TYPE_THEME = 'theme';
    const CONTEXT_TYPE_LIB = 'lib';
    /**#@-*/

    /**
     * File mask for certain file type
     */
    const FILE_MASK = null;

    /**
     * @var array
     */
    protected $_phrases = array();

    /**
     * @var array
     */
    protected $_defaultPathes = array();

    /**
     * @var string
     */
    private $_scanPath;

    /**
     * @var string
     */
    private $_baseDir;

    /**
     * Collect phrases from files
     */
    abstract protected function _collectPhrases();

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->_scanPath = $path;
    }

    /**
     * @param string $path
     */
    public function setBaseDir($path)
    {
        $this->_baseDir = $path;
    }

    /**
     * Get collected phrases
     *
     * @return array
     */
    public function getPhrases()
    {
        $this->_collectPhrases();
        return $this->_phrases;
    }

    /**
     * Add phrase
     *
     * @param string $phrase
     * @param \SplFileInfo $file
     * @param string|int $line
     * @throws \Exception
     */
    protected function _addPhrase($phrase, $file, $line = '')
    {
        if (!$phrase) {
            throw new \Exception(sprintf('Phrase cannot be empty. File: "%s" Line: "%s"', $file->getRealPath(), $line));
        }
        $phrase = $this->clearPhrase($phrase);
        $context = $this->_getContext($file->getRealPath());
        $contextType = $context[0];
        $contextValue = $context[1];
        $phraseData = array(
            'phrase' => $phrase,
            'file' => $file,
            'line' => $line,
            'context' => array($contextValue => 1),
            'context_type' => $contextType
        );
        $phraseKey = $contextType . '::' . $phrase;
        if (isset($this->_phrases[$phraseKey])) {
            $this->_phrases[$phraseKey]['context'][$contextValue] = 1;
        } else {
            $this->_phrases[$phraseKey] = $phraseData;
        }
    }

    /**
     * Clear phrase
     *
     * @param string $phrase
     * @return string
     */
    protected function clearPhrase($phrase)
    {
        $firstQuotes = $phrase[0];
        if ($firstQuotes != '"' && $firstQuotes != "'") {
            return $phrase;
        }
        $phrase = substr($phrase, 1);
        $phrase = substr($phrase, 0, -1);
        $phrase = str_replace('\\' . $firstQuotes, $firstQuotes, $phrase);
        return $phrase;
    }

    /**
     * Get context from file path in array(<context type>, <context value>) format
     * - for module: <Namespace>_<module name>
     * - for theme: <area>/<theme name>
     * - for lib: relative path to file
     *
     * @param string $filePath
     * @return array
     * @throws \Exception
     */
    protected function _getContext($filePath)
    {
        if (($contextValue = strstr($filePath, '/app/code/'))) {
            $contextType = self::CONTEXT_TYPE_MODULE;
            $contextValue = explode('/', $contextValue);
            $contextValue = $contextValue[3] . '_' . $contextValue[4];
        } elseif (($contextValue = strstr($filePath, '/app/design/'))) {
            $contextType = self::CONTEXT_TYPE_THEME;
            $contextValue = explode('/', $contextValue);
            $contextValue = $contextValue[3] . '/' . $contextValue[4];
        } elseif (($contextValue = strstr($filePath, '/pub/lib/'))) {
            $contextType = self::CONTEXT_TYPE_LIB;
        } else {
            throw new \Exception('Invalid path given: ' . $filePath);
        }
        return array($contextType, $contextValue);
    }

    /**
     * Get array of pathes for scan
     *
     * @return array
     */
    protected function _getScanPathes()
    {
        return $this->_getPath() ? array($this->_getPath()) : $this->_getDefaultScanPathes($this->_defaultPathes);
    }

    /**
     * Return array of absolute pathes for given relative pathes
     *
     * @return array
     */
    protected function _getDefaultScanPathes()
    {
        $baseDir = $this->_baseDir;
        return array_map(function ($path, $baseDir) use ($baseDir) {
            return $baseDir . $path;
        }, $this->_defaultPathes);
    }

    /**
     * @return string
     */
    protected function _getPath()
    {
        return $this->_scanPath;
    }

    /**
     * Collect files from give pathes by mask
     *
     * @throws \Exception
     */
    protected function _getFiles()
    {
        $pathes = $this->_getScanPathes();
        $files = array();
        foreach ($pathes as $path) {
            try {
                $filesIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            } catch (\UnexpectedValueException $valueException) {
                throw new \Exception(sprintf('Cannot read directory for scan phrase: "%s"', $path));
            }
            $filesIterator = new \RegexIterator($filesIterator, static::FILE_MASK);
            /** @var $file \SplFileInfo */
            foreach ($filesIterator as $file) {
                $files[] = $file;
            }
        }
        return $files;
    }
}
