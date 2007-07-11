<?php
/**
 * Fylesystem installer
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Installer_Filesystem extends Mage_Install_Model_Installer
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
        $this->_checkFilesystem();
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
        $config = Mage::getSingleton('install/config')->getPathForCheck();
        
        if (isset($config['writeable'])) {
            foreach ($config['writeable'] as $item) {
                $recursive = isset($item['recursive']) ? $item['recursive'] : false;
                $existence = isset($item['existence']) ? $item['existence'] : false;
                $res = $res && $this->_checkPath($item['path'], $recursive, $existence, 'write');
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
        $fullPath = dirname(Mage::getRoot()).$path;
        if ($mode == self::MODE_WRITE) {
            $setError = false;
            if ($existence) {
                if (!is_writable($fullPath)) {
                    $setError = true;
                }
            }
            else {
                if (file_exists($fullPath) && !is_writable($fullPath)) {
                    $setError = true;
                }
            }
            
            if ($setError) {
                Mage::getSingleton('install/session')->addError(
                    __('Path "%s" must be writable', $fullPath)
                );
                $res = false;
            }
        }
        
        if ($recursive && is_dir($fullPath)) {
            foreach (new DirectoryIterator($fullPath) as $file) {
                if (!$file->isDot() && $file->getFilename() != '.svn') {
                    $res = $res && $this->_checkPath($path.DS.$file->getFilename(), $recursive, $existence, $mode);
                }
            }
        }
        return $res;
    }
}