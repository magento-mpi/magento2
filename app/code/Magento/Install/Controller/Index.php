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
     * Filesystem facade
     *
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Config\Scope $configScope
     * @param \Magento\View\DesignInterface $viewDesign
     * @param \Magento\Core\Model\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\App $app
     * @param \Magento\App\State $appState
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Config\Scope $configScope,
        \Magento\View\DesignInterface $viewDesign,
        \Magento\Core\Model\Theme\CollectionFactory $collectionFactory,
        \Magento\Core\Model\App $app,
        \Magento\App\State $appState,
        \Magento\Filesystem $filesystem
    ) {
        $this->_filesystem = $filesystem;
        parent::__construct($context, $configScope, $viewDesign, $collectionFactory, $app, $appState);
    }

    /**
     * Dispatch event before action
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        if (!$this->_appState->isInstalled()) {
            $varDirectory = $this->_filesystem->getDirectoryWrite(\Magento\Filesystem\DirectoryList::VAR_DIR);
            foreach ($varDirectory->read() as $path) {
                if ($varDirectory->isDirectory($path)) {
                    $varDirectory->delete($path);
                }
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
