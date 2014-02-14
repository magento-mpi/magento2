<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor;

/**
 * Less file list
 */
class FileList implements \Iterator
{
    /**
     * @var File[]
     */
    protected $files = [];

    /**
     * Add file to list
     *
     * @param File $file
     * @return $this
     */
    public function addFile(File $file)
    {
        $this->files[$file->getFileIdentifier()] = $file;
        return $this;
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
