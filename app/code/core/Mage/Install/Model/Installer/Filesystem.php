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
     */
    protected function _checkFilesystem()
    {
        $config = Mage::getSingleton('install', 'config')->getPathForCheck();
        
        if (isset($config['writeable'])) {
            foreach ($config['writeable'] as $item) {
                $this->_checkPath($item['path'], $item['recursive'], 'write');
            }
        }
        return $this;
    }
    
    protected function _checkPath($path, $recursive, $mode)
    {
        $fullPath = dirname(Mage::getRoot()).$path;
        if ($mode == self::MODE_WRITE) {
            if (!is_writable($fullPath)) {
                Mage::getSingleton('install', 'session')->addMessage(
                    Mage::getModel('core', 'message')->error(__('Path "%s" must be writable', $fullPath))
                );
            }
        }
        
        if ($recursive && is_dir($fullPath)) {
            foreach (new DirectoryIterator($fullPath) as $file) {
                if (!$file->isDot() && $file->getFilename() != '.svn') {
                    $this->_checkPath($path.DS.$file->getFilename(), $recursive, $mode);
                }
            }
        }
        return $this;
    }
}