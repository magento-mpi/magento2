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
 * Install index controller
 */
namespace Magento\Install\Controller;

class Index extends \Magento\Install\Controller\Action
{
    /**
     * Core directory model
     *
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Config\Scope $configScope
     * @param \Magento\App\Dir $dir
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Config\Scope $configScope,
        \Magento\App\Dir $dir
    ) {
        parent::__construct($context, $configScope);
        $this->_dir = $dir;
    }

    /**
     * Dispatch event before action
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        if (!$this->_appState->isInstalled()) {
            foreach (glob($this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/*', GLOB_ONLYDIR) as $dir) {
                \Magento\Io\File::rmdirRecursive($dir);
            }
        }
        parent::preDispatch();
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_redirect('install/wizard/begin');
    }
}
