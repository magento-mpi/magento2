<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Connect\Package;

/**
 * Class to create archive.
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Writer
{
    /**
     * Name of package configuration file
     */
    const DEFAULT_NAME_PACKAGE_CONFIG = 'package.xml';

    /**
     * Temporary dir for extract DEFAULT_NAME_PACKAGE.
     */
    const PATH_TO_TEMPORARY_DIRECTORY = 'var/package/tmp/';

    /**
     * Files are used in package.
     *
     * @var array
     */
    protected $_files = array();

    /**
     * Archivator is used for extract DEFAULT_NAME_PACKAGE.
     *
     * @var \Magento\Framework\Archive
     */
    protected $_archivator = null;

    /**
     * Name of package with extension. Extension should be only one.
     * "package.tar.gz" is not ability, only "package.tgz".
     *
     * @var string
     */
    protected $_namePackage = 'package';

    /**
     * Temporary directory where package is situated.
     *
     * @var string
     */
    protected $_temporaryPackageDir = '';

    /**
     * Path to archive with package.
     *
     * @var string
     */
    protected $_pathToArchive = '';

    /**
     * Constructor initializes $_file.
     *
     * @param array $files
     * @param string $namePackage
     */
    public function __construct($files, $namePackage = '')
    {
        $this->_files = $files;
        $this->_namePackage = $namePackage;
        return $this;
    }

    /**
     * Retrieve archivator.
     *
     * @return \Magento\Framework\Archive
     */
    protected function _getArchivator()
    {
        if (is_null($this->_archivator)) {
            $this->_archivator = new \Magento\Framework\Archive();
        }
        return $this->_archivator;
    }

    /**
     * Create dir in PATH_TO_TEMPORARY_DIRECTORY and move all files
     * to this dir.
     *
     * @return $this
     */
    public function composePackage()
    {
        @mkdir(self::PATH_TO_TEMPORARY_DIRECTORY, 0777, true);
        $root = self::PATH_TO_TEMPORARY_DIRECTORY . basename($this->_namePackage);
        @mkdir($root, 0777, true);
        foreach ($this->_files as $file) {

            if (is_dir($file) || is_file($file)) {
                $fileName = basename($file);
                $filePath = dirname($file);
                @mkdir($root . '/' . $filePath, 0777, true);
                if (is_file($file)) {
                    copy($file, $root . '/' . $filePath . '/' . $fileName);
                } else {
                    @mkdir($root . '/' . $filePath . $fileName, 0777);
                }
            }
        }
        $this->_temporaryPackageDir = $root;
        return $this;
    }

    /**
     * Create dir in PATH_TO_TEMPORARY_DIRECTORY and move all files
     * to this dir.
     * This dir has a structure compatible with previous version of Magento Connact Manager
     *
     * @param array $destinationFiles
     * @return $this
     */
    public function composePackageV1x(array $destinationFiles)
    {
        @mkdir(self::PATH_TO_TEMPORARY_DIRECTORY, 0777, true);
        $root = self::PATH_TO_TEMPORARY_DIRECTORY . basename($this->_namePackage);
        @mkdir($root, 0777, true);
        $packageFilesDir = $root . '/' . basename($this->_namePackage);
        @mkdir($packageFilesDir, 0777, true);
        foreach ($this->_files as $index => $file) {
            $destinationFile = $destinationFiles[$index];
            if (is_dir($file) || is_file($file)) {
                $fileName = basename($destinationFile);
                $filePath = dirname($destinationFile);
                @mkdir($packageFilesDir . '/' . $filePath, 0777, true);
                if (is_file($file)) {
                    copy($file, $packageFilesDir . '/' . $filePath . '/' . $fileName);
                } else {
                    @mkdir($packageFilesDir . '/' . $filePath . $fileName, 0777);
                }
            }
        }
        $this->_temporaryPackageDir = $root;
        return $this;
    }

    /**
     * Add package.xml to temporary package directory.
     *
     * @param mixed $content
     * @return $this
     */
    public function addPackageXml($content)
    {
        file_put_contents($this->_temporaryPackageDir . '/' . self::DEFAULT_NAME_PACKAGE_CONFIG, $content);
        return $this;
    }

    /**
     * Archives package.
     *
     * @return $this
     */
    public function archivePackage()
    {
        $this->_pathToArchive = $this->_getArchivator()->pack(
            $this->_temporaryPackageDir,
            $this->_namePackage . '.tgz',
            true
        );

        //delete temporary dir
        \Magento\Framework\System\Dirs::rm(array("-r", $this->_temporaryPackageDir));
        return $this;
    }

    /**
     * Getter for pathToArchive
     *
     * @return string
     */
    public function getPathToArchive()
    {
        return $this->_pathToArchive;
    }
}
