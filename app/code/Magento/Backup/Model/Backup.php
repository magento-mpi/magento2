<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup file item model
 *
 * @method string getPath()
 * @method Magento_Backup_Model_Backup setPath() setPath($path)
 * @method string getName()
 * @method Magento_Backup_Model_Backup setName() setName($name)
 * @method string getTime()
 * @method Magento_Backup_Model_Backup setTime() setTime($time)
 */
class Magento_Backup_Model_Backup extends Magento_Object
{
    /* internal constants */
    const COMPRESS_RATE     = 9;

    /**
     * Type of backup file
     *
     * @var string
     */
    private $_type  = 'db';

    /**
     * Gz file pointer
     *
     * @var Magento_Filesystem_Stream_Zlib
     */
    protected $_stream = null;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Backup_Helper_Data
     */
    protected $_helper;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Locale model
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Backend auth session
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_backendAuthSession;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backup_Helper_Data $helper
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backup_Helper_Data $helper,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Backend_Model_Auth_Session $authSession,
        $data = array()
    ) {
        parent::__construct($data);

        $this->_coreData = $coreData;
        $adapter = new Magento_Filesystem_Adapter_Zlib(self::COMPRESS_RATE);
        $this->_filesystem = new Magento_Filesystem($adapter);
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_helper = $helper;
        $this->_locale = $locale;
        $this->_backendAuthSession = $authSession;
    }

    /**
     * Load backup file info
     *
     * @param string $fileName
     * @param string $filePath
     * @return Magento_Backup_Model_Backup
     */
    public function load($fileName, $filePath)
    {
        $backupData = $this->_helper->extractDataFromFilename($fileName);

        $this->addData(array(
            'id'   => $filePath . DS . $fileName,
            'time' => (int)$backupData->getTime(),
            'path' => $filePath,
            'extension' => $this->_helper->getExtensionByType($backupData->getType()),
            'display_name' => $this->_helper->nameToDisplayName($backupData->getName()),
            'name' => $backupData->getName(),
            'date_object' => new Zend_Date((int)$backupData->getTime(), $this->_locale->getLocaleCode())
        ));

        $this->setType($backupData->getType());
        return $this;
    }

    /**
     * Checks backup file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return $this->_filesystem->isFile($this->_getFilePath());
    }

    /**
     * Return file name of backup file
     *
     * @return string
     */
    public function getFileName()
    {
        $filename = $this->getTime() . "_" . $this->getType();
        $backupName = $this->getName();

        if (!empty($backupName)) {
            $filename .= '_' . $backupName;
        }

        $filename .= '.' . $this->_helper->getExtensionByType($this->getType());

        return $filename;
    }

    /**
     * Sets type of file
     *
     * @param string $value
     * @return Magento_Backup_Model_Backup
     */
    public function setType($value = 'db')
    {
        $possibleTypes = $this->_helper->getBackupTypesList();
        if (!in_array($value, $possibleTypes)) {
            $value = $this->_helper->getDefaultBackupType();
        }

        $this->_type = $value;
        $this->setData('type', $this->_type);

        return $this;
    }

    /**
     * Returns type of backup file
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set the backup file content
     *
     * @param string $content
     * @return Magento_Backup_Model_Backup
     * @throws Magento_Core_Exception
     */
    public function setFile(&$content)
    {
        if (!$this->hasData('time') || !$this->hasData('type') || !$this->hasData('path')) {
            throw new Magento_Core_Exception(__('Please correct the order of creation for a new backup.'));
        }

        $this->_filesystem->write($this->_getFilePath(), $content);
        return $this;
    }

    /**
     * Return content of backup file
     *
     * @return string
     * @throws Magento_Core_Exception
     */
    public function &getFile()
    {
        if (!$this->exists()) {
            throw new Magento_Core_Exception(__("The backup file does not exist."));
        }

        return $this->_filesystem->read($this->_getFilePath());
    }

    /**
     * Delete backup file
     *
     * @return Magento_Backup_Model_Backup
     * @throws Magento_Core_Exception
     */
    public function deleteFile()
    {
        if (!$this->exists()) {
            throw new Magento_Core_Exception(__("The backup file does not exist."));
        }

        $this->_filesystem->delete($this->_getFilePath());
        return $this;
    }

    /**
     * Open backup file (write or read mode)
     *
     * @param bool $write
     * @return Magento_Backup_Model_Backup
     * @throws Magento_Backup_Exception
     * @throws Magento_Backup_Exception_NotEnoughPermissions
     */
    public function open($write = false)
    {
        if (is_null($this->getPath())) {
            throw new Magento_Backup_Exception(__('The backup file path was not specified.'));
        }

        if ($write && $this->_filesystem->isFile($this->_getFilePath())) {
            $this->_filesystem->delete($this->_getFilePath());
        }
        if (!$write && !$this->_filesystem->isFile($this->_getFilePath())) {
            throw new Magento_Backup_Exception(__('The backup file "%1" does not exist.', $this->getFileName()));
        }

        $mode = $write ? 'wb' . self::COMPRESS_RATE : 'rb';

        try {
            $compressStream = 'compress.zlib://';
            $workingDirectory = $this->_filesystem->getWorkingDirectory();
            $this->_stream = $this->_filesystem->createAndOpenStream($compressStream . $this->_getFilePath(), $mode,
                $compressStream . $workingDirectory);
        }
        catch (Magento_Filesystem_Exception $e) {
            throw new Magento_Backup_Exception_NotEnoughPermissions(
                __('Sorry, but we cannot read from or write to backup file "%1".', $this->getFileName())
            );
        }

        return $this;
    }

    /**
     * Get zlib handler
     *
     * @return Magento_Filesystem_Stream_Zlib
     * @throws Magento_Backup_Exception
     */
    protected function _getStream()
    {
        if (is_null($this->_stream)) {
            throw new Magento_Backup_Exception(__('The backup file handler was unspecified.'));
        }
        return $this->_stream;
    }

    /**
     * Read backup uncomressed data
     *
     * @param int $length
     * @return string
     */
    public function read($length)
    {
        return $this->_getStream()->read($length);
    }

    /**
     * Check end of file.
     *
     * @return bool
     */
    public function eof()
    {
        return $this->_getStream()->eof();
    }

    /**
     * Write to backup file
     *
     * @param string $string
     * @return Magento_Backup_Model_Backup
     * @throws Magento_Backup_Exception
     */
    public function write($string)
    {
        try {
            $this->_getStream()->write($string);
        }
        catch (Magento_Filesystem_Exception $e) {
            throw new Magento_Backup_Exception(__('Something went wrong writing to the backup file "%1".',
                $this->getFileName()));
        }

        return $this;
    }

    /**
     * Close open backup file
     *
     * @return Magento_Backup_Model_Backup
     */
    public function close()
    {
        $this->_getStream()->close();
        $this->_stream = null;

        return $this;
    }

    /**
     * Print output
     */
    public function output()
    {
        if (!$this->exists()) {
            return ;
        }

        $stream = $this->_filesystem->createAndOpenStream($this->_getFilePath(), 'r');
        while ($buffer = $stream->read(1024)) {
            echo $buffer;
        }
        $stream->close();
    }

    /**
     * @return int|mixed
     */
    public function getSize()
    {
        if (!is_null($this->getData('size'))) {
            return $this->getData('size');
        }

        if ($this->exists()) {
            $this->setData('size', $this->_filesystem->getFileSize($this->_getFilePath()));
            return $this->getData('size');
        }

        return 0;
    }

    /**
     * Validate user password
     *
     * @param string $password
     * @return bool
     */
    public function validateUserPassword($password)
    {
        $userPasswordHash = $this->_backendAuthSession->getUser()->getPassword();
        return $this->_coreData->validateHash($password, $userPasswordHash);
    }

    /**
     * Load backup by it's type and creation timestamp
     *
     * @param int $timestamp
     * @param string $type
     * @return Magento_Backup_Model_Backup
     */
    public function loadByTimeAndType($timestamp, $type)
    {
        $backupsCollection = Mage::getSingleton('Magento_Backup_Model_Fs_Collection');
        $backupId = $timestamp . '_' . $type;

        foreach ($backupsCollection as $backup) {
            if ($backup->getId() == $backupId) {
                $this->setType($backup->getType())
                    ->setTime($backup->getTime())
                    ->setName($backup->getName())
                    ->setPath($backup->getPath());
                break;
            }
        }

        return $this;
    }

    /**
     * Get file path.
     *
     * @return string
     */
    protected function _getFilePath()
    {
        return $this->getPath() . DS . $this->getFileName();
    }
}
