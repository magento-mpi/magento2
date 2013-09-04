<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Loader\File;

use Magento\Tools\I18n\Code\Dictionary\Loader\FileInterface;
use Magento\Tools\I18n\Code\Factory;

/**
 *  Abstract dictionary loader from file
 */
abstract class AbstractFile implements FileInterface
{
    /**
     * Domain abstract factory
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $_factory;

    /**
     * File handler
     *
     * @var resource
     */
    protected $_fileHandler;

    /**
     * Current row position
     *
     * @var int
     */
    protected $_position;

    /**
     * Loader construct
     *
     * @param \Magento\Tools\I18n\Code\Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->_factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $this->_openFile($file);
        $dictionary = $this->_createDictionary();

        while ($data = $this->_readFile()) {
            $this->_position++;
            $dictionary->addPhrase($this->_createPhrase(array(
                'phrase' => isset($data[0]) ? $data[0] : null,
                'translation' => isset($data[1]) ? $data[1] : null,
                'contextType' => isset($data[2]) ? $data[2] : null,
                'contextValue' => isset($data[3]) ? $data[3] : null,
                'line' => $this->_position,
            )));
        }
        $this->_closeFile();

        return $dictionary;
    }

    /**
     * Init file handler
     *
     * @param string $file
     * @throws \InvalidArgumentException
     */
    protected function _openFile($file)
    {
        if (false === ($this->_fileHandler = @fopen($file, 'r'))) {
            throw new \InvalidArgumentException(sprintf('Cannot open dictionary file: "%s"', $file));
        }
        $this->_position = 0;
    }

    /**
     * Read file. Template method
     *
     * @return array
     */
    abstract protected function _readFile();

    /**
     * Close file handler
     */
    protected function _closeFile()
    {
        fclose($this->_fileHandler);
    }

    /**
     * Create dictionary
     *
     * @return \Magento\Tools\I18n\Code\Dictionary
     */
    protected function _createDictionary()
    {
        return $this->_factory->createDictionary();
    }

    /**
     * Create phrase
     *
     * @param array $data
     * @return \Magento\Tools\I18n\Code\Dictionary\Phrase
     * @throws \RuntimeException
     */
    protected function _createPhrase($data)
    {
        try {
            return $this->_factory->createPhrase($data);
        } catch (\DomainException $e) {
            throw new \RuntimeException(sprintf('Invalid row #%d: "%s".', $this->_position, $e->getMessage()));
        }
    }
}
