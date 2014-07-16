<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller;

use Magento\Framework\App\RequestInterface;

/**
 * Installation wizard controller
 */
class Wizard extends \Magento\Framework\App\Action\Action
{
    /**
     * Application state
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Installer Model
     *
     * @var \Magento\Install\Model\Installer
     */
    protected $_installer;

    /**
     * Install Wizard
     *
     * @var \Magento\Install\Model\Wizard
     */
    protected $_wizard;

    /**
     * Install Session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * DB Updater
     *
     * @var \Magento\Framework\Module\UpdaterInterface
     */
    protected $_dbUpdater;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Config\Scope $configScope
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $wizard
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\Module\UpdaterInterface $dbUpdater
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Config\Scope $configScope,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $wizard,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Module\UpdaterInterface $dbUpdater,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $configScope->setCurrentScope('install');
        $this->_installer = $installer;
        $this->_wizard = $wizard;
        $this->_session = $session;
        $this->_dbUpdater = $dbUpdater;
        $this->_appState = $appState;
    }

    /**
     * Perform necessary checks for all actions
     *
     * Redirect out if system is already installed
     * Throw a bootstrap exception if page cannot be displayed due to mis-configured base directories
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if ($this->_appState->isInstalled()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            $this->_redirect('/');
        }
        return parent::dispatch($request);
    }

    /**
     * Retrieve installer object
     *
     * @return \Magento\Install\Model\Installer
     */
    protected function _getInstaller()
    {
        return $this->_installer;
    }

    /**
     * Retrieve wizard
     *
     * @return \Magento\Install\Model\Wizard
     */
    protected function _getWizard()
    {
        return $this->_wizard;
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_view->loadLayout('install_wizard');
        $step = $this->_getWizard()->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }

        $this->_view->getLayout()->addBlock('Magento\Install\Block\State', 'install.state', 'left');
        return $this;
    }

    /**
     * Checking installation status
     *
     * @return bool
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _checkIfInstalled()
    {
        if ($this->_getInstaller()->isApplicationInstalled()) {
            $this->getResponse()->setRedirect($this->_storeManager->getStore()->getBaseUrl())->sendResponse();
            exit;
        }
        return true;
    }
}
