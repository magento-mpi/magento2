<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File;

use Magento\View\Layout\File;

/**
 * Unordered list of LESS file instances with awareness of LESS file identity
 */
class FileList
{
    /**
     * @var File[]
     */
    private $files = array();

    /**
     * Retrieve all LESS file instances
     *
     * @return File[]
     */
    public function getAll()
    {
        return array_values($this->files);
    }

    /**
     * Add LESS file instances to the list, preventing identity coincidence
     *
     * @param File[] $files
     * @throws \LogicException
     */
    public function add(array $files)
    {
        foreach ($files as $file) {
            $identifier = $this->getFileIdentifier($file);
            if (array_key_exists($identifier, $this->files)) {
                $filename = $this->files[$identifier]->getFilename();
                throw new \LogicException(
                    "LESS file '{$file->getFilename()}' is indistinguishable from the file '{$filename}'."
                );
            }
            $this->files[$identifier] = $file;
        }
    }

    /**
     * Replace already added LESS files with specified ones, checking for identity match
     *
     * @param File[] $files
     * @throws \LogicException
     */
    public function replace(array $files)
    {
        foreach ($files as $file) {
            $identifier = $this->getFileIdentifier($file);
            if (!array_key_exists($identifier, $this->files)) {
                throw new \LogicException(
                    "Replacing LESS file '{$file->getFilename()}' does not match to any of the files."
                );
            }
            $this->files[$identifier] = $file;
        }
    }

    /**
     * Add or replace already added LESS files with specified ones, checking for identity match
     *
     * @param File[] $files
     * @throws \LogicException
     */
    public function override(array $files)
    {
        foreach ($files as $file) {
            $identifier = $this->getFileIdentifier($file);
            $this->files[$identifier] = $file;
        }
    }

    /**
     * Calculate unique identifier for a LESS file
     *
     * @param File $file
     * @return string
     */
    protected function getFileIdentifier(File $file)
    {
        //TODO: Seems theme is not used anyway

        $theme = ($file->getTheme() ? 'theme:' . $file->getTheme()->getFullPath() : 'base');
        return $theme . '|module:' . $file->getModule() . '|file:' . $file->getName();
    }
}
