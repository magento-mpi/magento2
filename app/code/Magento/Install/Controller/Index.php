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
    protected $_coreDir;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Config\Scope $configScope
     * @param \Magento\View\DesignInterface $viewDesign
     * @param \Magento\Core\Model\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\App $app
     * @param \Magento\App\State $appState
     * @param \Magento\App\Dir $coreDir
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Config\Scope $configScope,
        \Magento\View\DesignInterface $viewDesign,
        \Magento\Core\Model\Theme\CollectionFactory $collectionFactory,
        \Magento\Core\Model\App $app,
        \Magento\App\State $appState,
        \Magento\App\Dir $coreDir
    ) {
        parent::__construct($context, $configScope, $viewDesign, $collectionFactory, $app, $appState);
        $this->_coreDir = $coreDir;
    }

    /**
     * Dispatch event before action
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        if (!$this->_appState->isInstalled()) {
            foreach (glob($this->_coreDir->getDir(\Magento\App\Dir::VAR_DIR) . '/*', GLOB_ONLYDIR) as $dir) {
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
