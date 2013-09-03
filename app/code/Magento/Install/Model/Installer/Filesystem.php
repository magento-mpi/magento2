<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fylesystem installer
 *
 * @category   Magento
 * @package    Magento_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Install_Model_Installer_Filesystem extends Magento_Install_Model_Installer_Abstract
{
    /**#@+
     * @deprecated since 1.7.1.0
     */
    const MODE_WRITE = 'write';
    const MODE_READ  = 'read';
    /**#@- */

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\Filesystem $filesystem)
    {
        $this->_filesystem = $filesystem;
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
        $config = Mage::getSingleton('Magento_Install_Model_Config')->getWritableFullPathsForCheck();

        if (is_array($config)) {
            foreach ($config as $item) {
                $recursive = isset($item['recursive']) ? (bool)$item['recursive'] : false;
                $existence = isset($item['existence']) ? (bool)$item['existence'] : false;
                $checkRes = $this->_checkFullPath($item['path'], $recursive, $existence);
                $res = $res && $checkRes;
            }
        }
        return $res;
    }

    /**
     * Check file system path
     *
     * @deprecated since 1.7.1.0
     * @param   string $path
     * @param   bool $recursive
     * @param   bool $existence
     * @param   string $mode
     * @return  bool
     */
    protected function _checkPath($path, $recursive, $existence, $mode)
    {
        return $this->_checkFullPath(dirname(Mage::getRoot()) . $path, $recursive, $existence);
    }

    /**
     * Check file system full path
     *
     * @param  string $fullPath
     * @param  bool $recursive
     * @param  bool $existence
     * @return bool
     */
    protected function _checkFullPath($fullPath, $recursive, $existence)
    {
        $result = true;

        if ($recursive && $this->_filesystem->isDirectory($fullPath)) {
            $pathsToCheck = $this->_filesystem->getNestedKeys($fullPath);
            array_unshift($pathsToCheck, $fullPath);
        } else {
            $pathsToCheck = array($fullPath);
        }

        $skipFileNames = array('.svn', '.htaccess');
        foreach ($pathsToCheck as $pathToCheck) {
            if (in_array(basename($pathToCheck), $skipFileNames)) {
                continue;
            }

            if ($existence) {
                $setError = !$this->_filesystem->isWritable($fullPath);
            } else {
                $setError = $this->_filesystem->has($fullPath) && !$this->_filesystem->isWritable($fullPath);
            }

            if ($setError) {
                $this->_getInstaller()->getDataModel()->addError(
                    __('Path "%1" must be writable.', $pathToCheck)
                );
                $result = false;
            }
        }

        return $result;
    }
}
