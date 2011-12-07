<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fylesystem installer
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Filesystem extends Mage_Install_Model_Installer_Abstract
{
    const MODE_WRITE = 'write';
    const MODE_READ  = 'read';

    public function __construct()
    {
    }

    /**
     * Check and prepare file system
     *
     */
    public function install()
    {
        if (!$this->_checkFilesystem()) {
            throw new Exception();
        };
        return $this;
    }

    /**
     * Check file system by config
     *
     * @return bool
     */
    protected function _checkFilesystem()
    {
        $res = true;
        $config = Mage::getSingleton('Mage_Install_Model_Config')->getPathForCheck();

        if (isset($config['writeable'])) {
            foreach ($config['writeable'] as $item) {
                $recursive = isset($item['recursive']) ? $item['recursive'] : false;
                $existence = isset($item['existence']) ? $item['existence'] : false;
                $checkRes = $this->_checkPath($item['path'], $recursive, $existence, 'write');
                $res = $res && $checkRes;
            }
        }
        return $res;
    }

    /**
     * Check file system path
     *
     * @param   string $path
     * @param   bool $recursive
     * @param   bool $existence
     * @param   string $mode
     * @return  bool
     */
    protected function _checkPath($path, $recursive, $existence, $mode)
    {
        $res = true;
        $fullPath = dirname(Mage::getRoot()) . $path;
        if ($mode == self::MODE_WRITE) {
            $setError = false;
            if ($existence) {
                if ((is_dir($fullPath) && !is_dir_writeable($fullPath)) || !is_writable($fullPath)) {
                    $setError = true;
                }
            }
            else {
                if (file_exists($fullPath) && !is_writable($fullPath)) {
                    $setError = true;
                }
            }

            if ($setError) {
                $this->_getInstaller()->getDataModel()->addError(
                    Mage::helper('Mage_Install_Helper_Data')->__('Path "%s" must be writable.', $fullPath)
                );
                $res = false;
            }
        }

        if ($recursive && is_dir($fullPath)) {
            foreach (new DirectoryIterator($fullPath) as $file) {
                if (!$file->isDot() && $file->getFilename() != '.svn' && $file->getFilename() != '.htaccess') {
                    $res = $res && $this->_checkPath($path . DS . $file->getFilename(), $recursive, $existence, $mode);
                }
            }
        }
        return $res;
    }
}
