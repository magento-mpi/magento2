<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Pack\Writer\File;

use Magento\Tools\I18n\Code\Context;
use Magento\Tools\I18n\Code\Dictionary;
use Magento\Tools\I18n\Code\Factory;
use Magento\Tools\I18n\Code\Locale;
use Magento\Tools\I18n\Code\Pack\WriterInterface;

/**
 * Abstract pack writer
 */
abstract class AbstractFile implements WriterInterface
{
    /**
     * Context
     *
     * @var \Magento\Tools\I18n\Code\Context
     */
    protected $_context;

    /**
     * Dictionary loader. This object is need for read dictionary for merge mode
     *
     * @var \Magento\Tools\I18n\Code\Dictionary\Loader\FileInterface
     */
    protected $_dictionaryLoader;

    /**
     * Domain abstract factory
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $_factory;

    /**
     * Pack path
     *
     * @var string
     */
    protected $_packPath;

    /**
     * Locale
     *
     * @var \Magento\Tools\I18n\Code\Locale
     */
    protected $_locale;

    /**
     * Save mode. One of const of WriterInterface::MODE_
     *
     * @var string
     */
    protected $_mode;

    /**
     * Writer construct
     *
     * @param \Magento\Tools\I18n\Code\Context $context
     * @param \Magento\Tools\I18n\Code\Dictionary\Loader\FileInterface $dictionaryLoader
     * @param \Magento\Tools\I18n\Code\Factory $factory
     */
    public function __construct(Context $context, Dictionary\Loader\FileInterface $dictionaryLoader, Factory $factory)
    {
        $this->_context = $context;
        $this->_dictionaryLoader = $dictionaryLoader;
        $this->_factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Dictionary $dictionary, $packPath, Locale $locale, $mode = self::MODE_REPLACE)
    {
        $this->_packPath = rtrim($packPath, '\\/') . '/';
        $this->_locale = $locale;
        $this->_mode = $mode;

        foreach ($this->_buildPackFilesData($dictionary) as $file => $phrases) {
            $this->_createDirectoryIfNotExist(dirname($file));
            $this->_writeFile($file, $phrases);
        }
    }

    /**
     * Create one pack file. Template method
     *
     * @param string $file
     * @param array $phrases
     * @throws \RuntimeException
     */
    abstract public function _writeFile($file, $phrases);

    /**
     * Build pack files data
     *
     * @param \Magento\Tools\I18n\Code\Dictionary $dictionary
     * @return array
     * @throws \RuntimeException
     */
    protected function _buildPackFilesData(Dictionary $dictionary)
    {
        $files = array();
        foreach ($dictionary->getPhrases() as $key => $phrase) {
            if (!$phrase->getContextType() || !$phrase->getContextValue()) {
                throw new \RuntimeException(sprintf('Missed context in row #%d.', $key + 1));
            }
            foreach ($phrase->getContextValue() as $context) {
                $path = $this->_context->buildPathToLocaleDirectoryByContext($phrase->getContextType(), $context);
                $filename = $this->_packPath . $path . $this->_locale . '.' . $this->_getFileExtension();
                $files[$filename][$phrase->getPhrase()] = $phrase;
            }
        }
        return $files;
    }

    /**
     * Get file extension
     *
     * @return string
     */
    abstract protected function _getFileExtension();

    /**
     * Create directory if not exists
     *
     * @param string $destinationPath
     * @param int $mode
     * @param bool $recursive Allows the creation of nested directories specified in the $destinationPath
     */
    protected function _createDirectoryIfNotExist($destinationPath, $mode = 0755, $recursive = true)
    {
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, $mode, $recursive);
            if ($mode) {
                chmod($destinationPath, $mode);
            }
        }
    }
}
