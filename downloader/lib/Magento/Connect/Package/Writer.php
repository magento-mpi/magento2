<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to create archive.
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Package_Writer
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
    * @var Magento_Archive
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
    * @var mixed
    */
    protected $_pathToArchive = '';

    /**
    * Constructor initializes $_file.
    *
    * @param array $files
    * @param string $namePackage
    * @return Magento_Connect_Package_Reader
    */
    public function __construct($files, $namePackage='')
    {
        $this->_files = $files;
        $this->_namePackage = $namePackage;
        return $this;
    }

    /**
    * Retrieve archivator.
    *
    * @return Magento_Archive
    */
    protected function _getArchivator()
    {
        if (is_null($this->_archivator)) {
            $this->_archivator = new Magento_Archive();
        }
        return $this->_archivator;
    }

    /**
    * Create dir in PATH_TO_TEMPORARY_DIRECTORY and move all files
    * to this dir.
    *
    * @return Magento_Connect_Package_Writer
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
                @mkdir($root . DS . $filePath, 0777, true);
                if (is_file($file)) {
                    copy($file, $root . DS . $filePath . DS . $fileName);
                } else {
                    @mkdir($root . DS . $filePath . $fileName, 0777);
                }
            }
        }
        $this->_temporaryPackageDir = $root;
        return $this;
    }

    /**
    * Add package.xml to temporary package directory.
    *
    * @param $content
    * @return Magento_Connect_Package_Writer
    */
    public function addPackageXml($content)
    {
        file_put_contents($this->_temporaryPackageDir . DS . self::DEFAULT_NAME_PACKAGE_CONFIG, $content);
        return $this;
    }

    /**
    * Archives package.
    *
    * @return Magento_Connect_Package_Writer
    */
    public function archivePackage()
    {
        $this->_pathToArchive = $this->_getArchivator()->pack(
            $this->_temporaryPackageDir,
            $this->_namePackage.'.tgz',
            true);

        //delete temporary dir
        Magento_System_Dirs::rm(array("-r", $this->_temporaryPackageDir));
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
