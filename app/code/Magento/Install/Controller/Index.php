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
     * @var \Magento\Core\Model\Dir
     */
    protected $_coreDir;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Config\Scope $configScope
     * @param \Magento\Core\Model\View\DesignInterface $viewDesign
     * @param \Magento\Core\Model\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\App\State $appState
     * @param \Magento\Core\Model\Dir $coreDir
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Config\Scope $configScope,
        \Magento\Core\Model\View\DesignInterface $viewDesign,
        \Magento\Core\Model\Theme\CollectionFactory $collectionFactory,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\App\State $appState,
        \Magento\Core\Model\Dir $coreDir
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
            foreach (glob($this->_coreDir->getDir(\Magento\Core\Model\Dir::VAR_DIR) . '/*', GLOB_ONLYDIR) as $dir) {
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
        $this->_forward('begin', 'wizard', 'install');
    }
}
