<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;
use Magento\Tools\I18n\Code\Dictionary\Scanner\FileScanner;

/**
 * Split dictionary by language pack
 */
class LanguagePack
{
    /**
     * i18n directory
     */
    const I18N_DIRECTORY = 'i18n/';

    /**
     * File mask for nee i18n directory
     */
    const I18N_NEW_DIRECTORY_MASK = 0755;

    /**
     * Deny locale
     */
    const DENY_LOCALE = 'en_US';

    /**
     * The biggest row length including phrase, translate and meta-info
     */
    const MAX_PHRASE_ROW_LENGTH = 1000;

    /**#@+
     * Save pack mode
     */
    const MODE_REPLACE = 'replace';
    const MODE_MERGE = 'merge';
    /**#@-*/

    /**
     * @var string
     */
    protected $_baseDir;

    /**
     * If false, will throw error if duplicated finded
     * If phrase exist more than once into one of code-pool-type (meta-info-type), than it is considered as duplicate
     *
     * @var bool
     */
    protected $_allowDuplicates = false;

    /**
     * @var string
     */
    protected $_targetLocale;

    /**
     * @var string
     */
    protected $_saveMode;

    /**
     * @var array
     */
    protected $_allowedSaveModes = array(
        'replace', 'merge'
    );

    /**
     * @var string
     */
    protected $_sourceFilename;

    /**
     * @var resource
     */
    private $_sourceHandler = null;

    /**
     * @var array
     */
    private $_translateDuplicates = array();

    /**
     * @var int
     */
    private $_savedPackN = 0;

    /**
     * @param string $baseDir
     */
    public function __construct($baseDir)
    {
        $this->_baseDir = $baseDir;
    }

    /**
     * @param bool $allow
     */
    public function setAlloweDuplicates($allow)
    {
        $this->_allowDuplicates = $allow;
    }

    /**
     * @param string $locale
     */
    public function setTargetLocale($locale)
    {
        $this->_targetLocale = $locale;
    }

    /**
     * @param string $filename
     */
    public function setSourceFilename($filename)
    {
        $this->_sourceFilename = $filename;
    }

    /**
     * @param string $saveMode
     */
    public function setSaveMode($saveMode)
    {
        if (!in_array($saveMode, $this->_allowedSaveModes)) {
            $this->_throwError('Save mode is wrong. allowed modes: "%s"', implode(', ', $this->_allowedSaveModes));
        }
        $this->_saveMode = $saveMode;
    }

    /**
     * Split dictionary into language pack
     *
     * @throws \Exception
     */
    public function splitDictionary()
    {
        $this->_init();
        $languagePack = $this->_getLanguagePack();
        $this->_processLanguagePack($languagePack);
    }

    /**
     * @return string
     */
    public function getSuccessSavedMessage()
    {
        if ($this->_savedPackN) {
            return sprintf("\nSucessfully saved %d language package(s)", $this->_savedPackN);
        }
        return '';
    }

    /**
     * Init. Create handler for source. Check requires params
     *
     * @throws \Exception
     */
    private function _init()
    {
        $this->_checkTargetLocale();
        $this->_targetLocale .= '.csv';

        $sourceHandler = false;
        if (!$this->_sourceFilename) {
            $sourceHandler = STDIN;
        } elseif (is_readable($this->_sourceFilename)) {
            $sourceHandler = fopen($this->_sourceFilename, 'r');
        }
        if (!$sourceHandler) {
            $this->_throwError('Cannot read from source file: "%s"', $this->_sourceFilename);
        }
        $this->_sourceHandler = $sourceHandler;
    }

    /**
     * Check target locale
     */
    private function _checkTargetLocale()
    {
        $targetLocale = $this->_targetLocale;
        if (!$targetLocale) {
            $this->_throwError('Target locale is required.');
        } elseif ($targetLocale == self::DENY_LOCALE) {
            $this->_throwError('Target locale cannot equal to "%s"', self::DENY_LOCALE);
        } elseif (!preg_match('/[a-z]{2}_[A-Z]{2}/', $targetLocale)) {
            $this->_throwError('Target locale must match the following format: "%s"', "aa_AA");
        }
    }

    /**
     * @return array
     */
    private function _getLanguagePack()
    {
        $rowN = 0;
        $languagePack = array();
        while (($phraseRow = $this->_getPhraseRow($this->_getSourceHandler())) != false) {
            $rowN++;
            $phrase = $phraseRow[0];
            $translate = $phraseRow[1];
            $contextType = isset($phraseRow[2]) ? $phraseRow[2] : false;
            $contextValue = isset($phraseRow[3]) ? $phraseRow[3] : false;
            // empty row
            if (!$phrase && !$translate && !$contextValue && !$contextType) {
                continue;
            }
            if (!$phrase || !$translate) {
                $this->_throwError('Missed phrase in row #%d: %s', $rowN, implode($phraseRow));
            }
            if (!$contextType || !$contextValue) {
                $this->_throwError(
                    'Cannot split dictionary into language pack. Context infromation is absent in row#%d: %s',
                    $rowN, implode($phraseRow)
                );
            }
            $this->_collectDuplicates($phrase, $translate);
            $languagePack[$contextType][$contextValue][] = array($phrase, $translate);
        }
        $this->_closeSourceHandler();
        $this->_throwErrorMessageOnDuplicates();
        return $languagePack;
    }

    /**
     * Get dictionary handler
     *
     * @return resource
     */
    private function _getSourceHandler()
    {
        return $this->_sourceHandler;
    }

    /**
     * @return bool
     */
    private function _closeSourceHandler()
    {
        return fclose($this->_sourceHandler);
    }

    /**
     * @param resource $fileResource
     * @return array
     */
    private function _getPhraseRow($fileResource)
    {
        return fgetcsv($fileResource, self::MAX_PHRASE_ROW_LENGTH, ',', '"');
    }

    /**
     * Save language pack into the specific files
     *
     * @param array $languagePack
     * @throws \Exception
     */
    private function _processLanguagePack($languagePack)
    {
        foreach ($languagePack as $contextType => $dictionaryData) {
            foreach ($dictionaryData as $contextValue => $dictionary) {
                $directories = $this->_getDictionaryPathes($contextType, $contextValue);
                foreach ($directories as $directory) {
                    $this->_saveLanguagePack($directory, $dictionary);
                }
            }
        }
    }

    /**
     * Save language pack to the directory
     *
     * @param $directory
     * @param $dictionary
     */
    private function _saveLanguagePack($directory, $dictionary)
    {
        $languagePackFile = $directory . $this->_targetLocale;
        if ($this->_saveMode == self::MODE_MERGE) {
            $dictionary = $this->_mergeDictionaries($languagePackFile, $dictionary);
        }
        $fileHandler = fopen($languagePackFile, 'w');
        if (false === $fileHandler) {
            $this->_throwError('Cannot write language pack to file: "%s:', $languagePackFile);
        }
        foreach ($dictionary as $phraseVsTranslate) {
            fputcsv($fileHandler, $phraseVsTranslate, ',', '"');
        }
        $this->_savedPackN++;
        fclose($fileHandler);
    }

    /**
     * @param string $languagePackFile
     * @param array $dictionary
     * @return array
     */
    private function _mergeDictionaries($languagePackFile, $dictionary)
    {
        if (!file_exists($languagePackFile)) {
            return $dictionary;
        }
        $fileHandler = fopen($languagePackFile, 'r');
        if (!$fileHandler) {
            $this->_throwError('Cannot read file "%s"', $languagePackFile);
        }
        $mergedDictionary = array();
        $merged = array();
        while (($phraseRow = $this->_getPhraseRow($fileHandler)) != false) {
            $merged[$phraseRow[0]] = $phraseRow[1];
        }
        fclose($fileHandler);
        foreach ($dictionary as $phrase) {
            $merged[$phrase[0]] = $phrase[1];
        }
        foreach ($merged as $phrase => $translate) {
            $mergedDictionary[] = array($phrase, $translate);
        }
        return $mergedDictionary;
    }

    /**
     * Get absolute directory path for given meta information
     *
     * @param string $contextType
     * @param string $contextValue
     * @return array
     * @throws \Exception
     */
    private function _getDictionaryPathes($contextType, $contextValue)
    {
        $pathes = array();
        $contextValues = explode(',', $contextValue);
        foreach ($contextValues as $context) {
            $path = $this->_baseDir . '/app/';
            switch ($contextType) {
                case FileScanner::CONTEXT_TYPE_MODULE:
                    $path .= 'code/' . str_replace('_', '/', $context) . '/' . self::I18N_DIRECTORY;
                    break;
                case FileScanner::CONTEXT_TYPE_THEME:
                    $path .= 'design/' . $context . '/' . self::I18N_DIRECTORY;
                    break;
                case FileScanner::CONTEXT_TYPE_LIB:
                    $path .= self::I18N_DIRECTORY;
                    break;
                default:
                    $this->_throwError('Invalid meta-type given: ' . $contextType);
            }
            $this->_createDirectoryIfNo($path);
            $pathes[] = $path;
        }
        return $pathes;
    }

    /**
     * Create i18n directory if it not exist
     *
     * @param string $path
     * @throws \Exception
     */
    private function _createDirectoryIfNo($path)
    {
        if (!file_exists($path) || !is_dir($path)) {
            $previousDir = strstr($path, self::I18N_DIRECTORY, true);
            if (!is_dir($previousDir)) {
                $this->_throwError('Parent directory does not exist: ' . $previousDir);
            }
            if (!mkdir($path, self::I18N_NEW_DIRECTORY_MASK, false)) {
                $this->_throwError('Cannot create directory "i18n" into the parent: ' . $path);
            }
        }
    }

    /**
     * @param string $phrase
     * @param string $translate
     */
    private function _collectDuplicates($phrase, $translate)
    {
        if (!$this->_allowDuplicates) {
            $this->_translateDuplicates[$phrase][$translate] += 1;
        }
    }

    /**
     * Throw an error if translate duplicates is exist
     *
     * @throws \Exception
     */
    private function _throwErrorMessageOnDuplicates()
    {
        if (!$this->_allowDuplicates) {
            $this->_translateDuplicates = array_filter($this->_translateDuplicates, function ($translate) {
                return count($translate) != 1 ? true : false;
            });
            $error = '';
            foreach ($this->_translateDuplicates as $phrase => $translations) {
                $error .= sprintf("Error. The phrase \"%s\" is translated differently in different places: %s\n",
                    $phrase, $this->_getTranslationPlacesError($translations));
            }
            if ($error) {
                $this->_throwError($error);
            }
        }
    }

    /**
     * Get translations places error message
     *
     * @param array $translations
     * @return string
     */
    private function _getTranslationPlacesError($translations)
    {
        $error = array();
        foreach ($translations as $translate => $translateAmount) {
            $error[] = sprintf('"%s" in %d place(s)', $translate, $translateAmount);
        }
        return implode(', ', $error);
    }

    /**
     * Throw an exception
     *
     * @throws \Exception
     */
    private function _throwError()
    {
        $args = func_get_args();
        $message = array_shift($args);
        $message .= $this->getSuccessSavedMessage();
        throw new \Exception(vsprintf($message, $args));
    }
}
