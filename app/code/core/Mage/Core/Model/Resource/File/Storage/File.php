<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Model for synchronization from DB to filesystem
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_File_Storage_File
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_mediaBaseDirectory = null;

    /**
     * Files at storage
     *
     * @var array
     */
    public function getMediaBaseDirectory()
    {
        if (is_null($this->_mediaBaseDirectory)) {
            $this->_mediaBaseDirectory = Mage::helper('Mage_Core_Helper_File_Storage_Database')->getMediaBaseDir();
        }

        return $this->_mediaBaseDirectory;
    }

    /**
     * Collect files and directories recursively
     *
     * @param  string$dir
     * @return array
     */
    public function getStorageData($dir = '')
    {
        $files          = array();
        $directories    = array();
        $currentDir     = $this->getMediaBaseDirectory() . $dir;

        if (is_dir($currentDir)) {
            $dh = opendir($currentDir);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..' || $file == '.svn' || $file == '.htaccess') {
                        continue;
                    }

                    $fullPath = $currentDir . DS . $file;
                    $relativePath = $dir . DS . $file;
                    if (is_dir($fullPath)) {
                        $directories[] = array(
                            'name' => $file,
                            'path' => str_replace(DS, '/', ltrim($dir, DS))
                        );

                        $data = $this->getStorageData($relativePath);
                        $directories = array_merge($directories, $data['directories']);
                        $files = array_merge($files, $data['files']);
                    } else {
                        $files[] = $relativePath;
                    }
                }
                closedir($dh);
            }
        }

        return array('files' => $files, 'directories' => $directories);
    }

    /**
     * Clear files and directories in storage
     *
     * @param  string $dir
     * @return Mage_Core_Model_Resource_File_Storage_File
     */
    public function clear($dir = '')
    {
        $currentDir = $this->getMediaBaseDirectory() . $dir;

        if (is_dir($currentDir)) {
            $dh = opendir($currentDir);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }

                    $fullPath = $currentDir . DS . $file;
                    if (is_dir($fullPath)) {
                        $this->clear($dir . DS . $file);
                    } else {
                        @unlink($fullPath);
                    }
                }
                closedir($dh);
                @rmdir($currentDir);
            }
        }

        return $this;
    }

    /**
     * Save directory to storage
     *
     * @param  array $dir
     * @return bool
     */
    public function saveDir($dir)
    {
        if (!isset($dir['name']) || !strlen($dir['name'])
            || !isset($dir['path'])
        ) {
            return false;
        }

        $path = (strlen($dir['path']))
            ? $dir['path'] . DS . $dir['name']
            : $dir['name'];
        $path = Mage::helper('Mage_Core_Helper_File_Storage_Database')->getMediaBaseDir() . DS . str_replace('/', DS, $path);

        if (!file_exists($path) || !is_dir($path)) {
            if (!@mkdir($path, 0777, true)) {
                Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Unable to create directory: %s', $path));
            }
        }

        return true;
    }

    /**
     * Save file to storage
     *
     * @param  string $filePath
     * @param  string $content
     * @param  bool $overwrite
     * @return bool
     */
    public function saveFile($filePath, $content, $overwrite = false)
    {
        $filename = basename($filePath);
        $path = $this->getMediaBaseDirectory() . DS . str_replace('/', DS ,dirname($filePath));

        if (!file_exists($path) || !is_dir($path)) {
            @mkdir($path, 0777, true);
        }

        $ioFile = new Varien_Io_File();
        $ioFile->cd($path);

        if (!$ioFile->fileExists($filename) || ($overwrite && $ioFile->rm($filename))) {
            $ioFile->streamOpen($filename);
            $ioFile->streamLock(true);
            $result = $ioFile->streamWrite($content);
            $ioFile->streamUnlock();
            $ioFile->streamClose();

            if ($result) {
                return true;
            }

            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Unable to save file: %s', $filePath));
        }

        return false;
    }
}
