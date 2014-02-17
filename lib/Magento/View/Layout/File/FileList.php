<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File;

use Magento\View\Layout\File;
use Magento\View\Layout\File\FileList\CollateInterface;

/**
 * Unordered list of layout file instances with awareness of layout file identity
 */
class FileList
{
    /**
     * Array of files
     *
     * @var File[]
     */
    protected $files = array();

    /**
     * Collator
     *
     * @var CollateInterface
     */
    protected $collator;

    /**
     * Constructor
     *
     * @param CollateInterface $collator
     */
    public function __construct(CollateInterface $collator)
    {
        $this->collator = $collator;
    }

    /**
     * Retrieve all layout file instances
     *
     * @return File[]
     */
    public function getAll()
    {
        return array_values($this->files);
    }

    /**
     * Add layout file instances to the list, preventing identity coincidence
     *
     * @param File[] $files
     * @return void
     * @throws \LogicException
     */
    public function add(array $files)
    {
        foreach ($files as $file) {
            $identifier = $file->getFileIdentifier();
            if (array_key_exists($identifier, $this->files)) {
                $filename = $this->files[$identifier]->getFilename();
                throw new \LogicException(
                    "Layout file '{$file->getFilename()}' is indistinguishable from the file '{$filename}'."
                );
            }
            $this->files[$identifier] = $file;
        }
    }

    /**
     * Replace already added layout files with specified ones, checking for identity match
     *
     * @param File[] $files
     * @return void
     */
    public function replace(array $files)
    {
        $this->files = $this->collator->collate($files, $this->files);
    }
}
