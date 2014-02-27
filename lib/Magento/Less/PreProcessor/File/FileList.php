<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor\File;

/**
 * Less file list container
 */
class FileList implements \Iterator
{
    /**
     * @var LessFactory
     */
    protected $lessFactory;

    /**
     * Entry less file for this container
     *
     * @var Less
     */
    protected $initialFile;

    /**
     * @var Less[]
     */
    protected $files = [];

    /**
     * Constructor
     *
     * @param LessFactory $lessFactory
     * @param string $lessFilePath
     * @param array $viewParams
     * @throws \InvalidArgumentException
     */
    public function __construct(
        LessFactory $lessFactory,
        $lessFilePath = null,
        $viewParams = []
    ) {
        if (empty($lessFilePath) || empty($viewParams)) {
            throw new \InvalidArgumentException('FileList container must contain entry less file data');
        }
        $this->lessFactory = $lessFactory;
        $this->initialFile = $this->createFile($lessFilePath, $viewParams);
        $this->addFile($this->initialFile);
    }

    /**
     * Return entry less file for this container
     *
     * @return Less
     */
    public function getInitialFile()
    {
        return $this->initialFile;
    }

    /**
     * Return publication path of entry less file
     *
     * @return string
     */
    public function getPublicationPath()
    {
        return $this->initialFile->getPublicationPath();
    }

    /**
     * Add file to list
     *
     * @param Less $file
     * @return $this
     */
    public function addFile(Less $file)
    {
        $this->files[$file->getFileIdentifier()] = $file;
        return $this;
    }

    /**
     * Create instance of less file
     *
     * @param string $lessFilePath
     * @param array $viewParams
     * @return mixed
     */
    public function createFile($lessFilePath, $viewParams)
    {
        return $this->lessFactory->create(['filePath' => $lessFilePath, 'viewParams' => $viewParams]);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->files);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->files);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->files);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return (bool) current($this->files);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->files);
    }
}
