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
class Magento_Logging_Model_Archive extends \Magento\Object
{
    /**
     * Full system name to current file, if set
     *
     * @var string
     */
    protected $_file = '';

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Filesystem $fileSystem
     */
    public function __construct(\Magento\Filesystem $fileSystem)
    {
        $this->_filesystem = $fileSystem;
    }

    /**
     * Storage base path getter
     *
     * @return string
     */
    public function getBasePath()
    {
        return BP . DS . 'var' . DS . 'logging' . DS . 'archive';
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
     * @return Magento_Logging_Model_Archive
     */
    public function loadByBaseName($baseName)
    {
        $this->_file = '';
        $this->unsBaseName();
        if (!$this->_validateBaseName($baseName)) {
            return $this;
        }
        $filename = $this->generateFilename($baseName);
        if (!$this->_filesystem->isFile($filename)) {
            return $this;
        }
        $this->setBaseName($baseName);
        $this->_file = $filename;
        return $this;
    }

    /**
     * Generate a full system filename from base name
     *
     * @param string $baseName
     * @return Magento_Logging_Model_Archive
     */
    public function generateFilename($baseName)
    {
        return $this->getBasePath() . DS . substr($baseName, 0, 4) . DS . substr($baseName, 4, 2) . DS . $baseName;
    }

    /**
     * Full system filename getter
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_file;
    }

    /**
     * Get file contents, if any
     *
     * @return string
     */
    public function getContents()
    {
        if ($this->_file) {
            return $this->_filesystem
                ->setWorkingDirectory($this->getBasePath())
                ->read($this->_file);
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
        $this->_filesystem
            ->setWorkingDirectory($this->getBasePath())
            ->setIsAllowCreateDirectories(true, 755)
            ->touch($filename);

        $this->loadByBaseName($baseName);
        return true;
    }
}
