<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\App\Action\Plugin;

class Dir
{
    /**
     * Application state
     *
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * Directory list
     *
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * @param \Magento\App\State $state
     * @param \Magento\App\Dir $dir
     */
    public function __construct(\Magento\App\State $state, \Magento\App\Dir $dir)
    {
        $this->_appState = $state;
        $this->_dir = $dir;
    }

    /**
     * Clear temporary directories
     *
     * @param $arguments
     * @return mixed
     */
    public function beforeDispatch($arguments)
    {
        if (!$this->_appState->isInstalled()) {
            foreach (glob($this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/*', GLOB_ONLYDIR) as $dir) {
                \Magento\Io\File::rmdirRecursive($dir);
            }
        }
        return $arguments;
    }
} 