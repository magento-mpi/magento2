<?php
/**
 * The class, which verifies existence and write access to the needed application directories
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dir_Verification
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var array
     */
    protected $_writableDirCodes = array(
        Mage_Core_Model_Dir::MEDIA,
        Mage_Core_Model_Dir::STATIC_VIEW,
        Mage_Core_Model_Dir::VAR_DIR,
        Mage_Core_Model_Dir::TMP,
        Mage_Core_Model_Dir::CACHE,
        Mage_Core_Model_Dir::LOG,
        Mage_Core_Model_Dir::SESSION,
    );

    /**
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dirs
     * @param null|array $writableDirCodes Codes of directories that must be verified
     */
    public function __construct(Magento_Filesystem $filesystem, Mage_Core_Model_Dir $dirs, $writableDirCodes = null)
    {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        if ($writableDirCodes !== null) {
            $this->_writableDirCodes = $writableDirCodes;
        }
    }

    /**
     * Create the required application directories, if they are missed
     *
     * @return Mage_Core_Model_Dir_Verification
     * @throws Magento_BootstrapException
     */
    public function createMissingDirectories()
    {
        $fails = array();
        foreach ($this->_writableDirCodes as $dirCode) {
            $dir = $this->_dirs->getDir($dirCode);
            if ($this->_filesystem->isDirectory($dir)) {
                continue;
            }
            try {
                $this->_filesystem->createDirectory($dir);
            } catch (Magento_Filesystem_Exception $e) {
                $fails[] = $dir;
            }
        }

        if ($fails) {
            $dirList = str_replace('/', DIRECTORY_SEPARATOR, implode(',', $fails));
            throw new Magento_BootstrapException("Cannot create directory(ies), check write access: {$dirList}");
        }

        return $this;
    }

    /**
     * Check the directories for write access
     *
     * @return Mage_Core_Model_Dir_Verification
     * @throws Magento_BootstrapException
     */
    public function verifyWriteAccess()
    {
        $fails = array();
        foreach ($this->_writableDirCodes as $dirCode) {
            $dir = $this->_dirs->getDir($dirCode);
            if (!$this->_filesystem->isWritable($dir)) {
                $fails[] = $dir;
            }
        }
        if ($fails) {
            $dirList = str_replace('/', DIRECTORY_SEPARATOR, implode(',', $fails));
            throw new Magento_BootstrapException("The directory(ies) must have write access: {$dirList}");
        }

        return $this;
    }
}
