<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log archive file model
 */
namespace Magento\Logging\Model;

class Archive extends \Magento\Object
{
    /**
     * Full system name to current file, if set
     *
     * @var string
     */
    protected $file = '';

    /**
     * @var \Magento\Filesystem\Directory\Write
     */
    protected $directory;

    /**
     * @param \Magento\Filesystem $fileSystem
     */
    public function __construct(\Magento\Filesystem $fileSystem)
    {
        $this->directory = $fileSystem->getDirectoryWrite(\Magento\Filesystem::VAR_DIR);
    }

    /**
     * Storage base path getter
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->directory->getAbsolutePath('logging/archive');
    }

    /**
     * Check base name syntax
     *
     * @param string $baseName
     * @return bool
     */
    protected function _validateBaseName($baseName)
    {
        return (bool)preg_match('/^[0-9]{10}\.csv$/', $baseName);
    }

    /**
     * Search the file in storage by base name and set it
     *
     * @param string $baseName
     * @return \Magento\Logging\Model\Archive
     */
    public function loadByBaseName($baseName)
    {
        $this->file = '';
        $this->unsBaseName();
        if (!$this->_validateBaseName($baseName)) {
            return $this;
        }
        $filename = $this->generateFilename($baseName);
        if (!$this->directory->isFile($this->directory->getRelativePath($filename))) {
            return $this;
        }
        $this->setBaseName($baseName);
        $this->file = $filename;
        return $this;
    }

    /**
     * Generate a full system filename from base name
     *
     * @param string $baseName
     * @return \Magento\Logging\Model\Archive
     */
    public function generateFilename($baseName)
    {
        return $this->getBasePath() . '/' . substr($baseName, 0, 4) . '/' . substr($baseName, 4, 2) . '/' . $baseName;
    }

    /**
     * Full system filename getter
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->file;
    }

    /**
     * Get file contents, if any
     *
     * @return string
     */
    public function getContents()
    {
        if ($this->file) {
            return $this->directory->readFile($this->directory->getRelativePath($this->file));
        }
        return '';
    }

    /**
     * Mime-type getter
     *
     * @return string
     */
    public function getMimeType()
    {
        return 'text/csv';
    }

    /**
     * Attempt to create a new file using specified base name
     * Or generate a base name from current date/time
     *
     * @param string $baseName
     * @return bool
     */
    public function createNew($baseName = '')
    {
        if (!$baseName) {
            $baseName = date('YmdH') . '.csv';
        }
        if (!$this->_validateBaseName($baseName)) {
            return false;
        }

        $filename = $this->generateFilename($baseName);
        $this->directory->touch($this->directory->getRelativePath($filename));

        $this->loadByBaseName($baseName);
        return true;
    }
}
