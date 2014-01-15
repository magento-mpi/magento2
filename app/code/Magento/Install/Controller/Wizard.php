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
 * Installation wizard controller
 */
namespace Magento\Install\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Wizard extends \Magento\Install\Controller\Action
{
    /**
     * Application state
     *
     * @var \Magento\App\State
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
     * @var \Magento\Session\Generic
     */
    protected $_session;

    /**
     * DB Updater
     *
     * @var \Magento\Module\UpdaterInterface
     */
    protected $_dbUpdater;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Config\Scope $configScope
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $wizard
     * @param \Magento\Session\Generic $session
     * @param \Magento\Module\UpdaterInterface $dbUpdater
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Config\Scope $configScope,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $wizard,
        \Magento\Session\Generic $session,
        \Magento\Module\UpdaterInterface $dbUpdater,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\State $appState
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $configScope);
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
     * @return \Magento\App\ResponseInterface
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
     * @return \Magento\Install\Controller\Wizard
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
            $this->getResponse()
                ->setRedirect($this->_storeManager->getStore()->getBaseUrl())
                ->sendResponse();
            exit;
        }
        return true;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_redirect('*/*/begin');
    }

    /**
     * Begin installation action
     */
    public function beginAction()
    {
        $this->_checkIfInstalled();

        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();

        $this->_view->getLayout()->addBlock('Magento\Install\Block\Begin', 'install.begin', 'content');

        $this->_view->renderLayout();
    }

    /**
     * Process begin step POST data
     */
    public function beginPostAction()
    {
        $this->_checkIfInstalled();

        $agree = $this->getRequest()->getPost('agree');
        if ($agree && $step = $this->_getWizard()->getStepByName('begin')) {
            $this->getResponse()->setRedirect($step->getNextUrl());
        } else {
            $this->_redirect('install');
        }
    }

    /**
     * Localization settings
     */
    public function localeAction()
    {
        $this->_checkIfInstalled();
        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getLayout()->addBlock('Magento\Install\Block\Locale', 'install.locale', 'content');
        $this->_view->getLayout()
            ->getBlock('install.locale')
            ->setLocaleCode(
                $this->_session->getLocale()
            );
        $this->_view->renderLayout();
    }

    /**
     * Change current locale
     */
    public function localeChangeAction()
    {
        $this->_checkIfInstalled();

        $locale = $this->getRequest()->getParam('locale');
        $timezone = $this->getRequest()->getParam('timezone');
        $currency = $this->getRequest()->getParam('currency');
        if ($locale) {
            $this->_session->setLocale($locale)
                ->setTimezone($timezone)
                ->setCurrency($currency);
        }

        $this->_redirect('*/*/locale');
    }

    /**
     * Saving localization settings
     */
    public function localePostAction()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('locale');

        $data = $this->getRequest()->getPost('config');
        if ($data) {
            $this->_session->setLocaleData($data);
        }

        $this->getResponse()->setRedirect($step->getNextUrl());
    }

    /**
     * Download page action
     */
    public function downloadAction()
    {
        $this->_checkIfInstalled();
        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getLayout()->addBlock('Magento\Install\Block\Download', 'install.download', 'content');

        $this->_view->renderLayout();
    }

    /**
     * Download post action
     */
    public function downloadPostAction()
    {
        $this->_checkIfInstalled();
        switch ($this->getRequest()->getPost('continue')) {
            case 'auto':
                $this->_forward('downloadAuto');
                break;

            case 'manual':
                $this->_forward('downloadManual');
                break;

            case 'svn':
                $step = $this->_getWizard()->getStepByName('download');
                $this->getResponse()->setRedirect($step->getNextUrl());
                break;

            default:
                $this->_redirect('*/*/download');
                break;
        }
    }

    /**
     * Download auto action
     */
    public function downloadAutoAction()
    {
        $step = $this->_getWizard()->getStepByName('download');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }

    /**
     * Install action
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function installAction()
    {
        $pear = \Magento\Pear::getInstance();
        $params = array(
            'comment' => __("Downloading and installing Magento, please wait...") . "\r\n\r\n"
        );
        if ($this->getRequest()->getParam('do')) {
            $state = $this->getRequest()->getParam('state', 'beta');
            if ($state) {
                $result = $pear->runHtmlConsole(array(
                'comment'   => __("Setting preferred state to: %1", $state) . "\r\n\r\n",
                'command'   => 'config-set',
                'params'    => array('preferred_state', $state)
                ));
                if ($result instanceof PEAR_Error) {
                    $this->installFailureCallback();
                    exit;
                }
            }
            $params['command'] = 'install';
            $params['options'] = array('onlyreqdeps' => 1);
            $params['params'] = $this->_objectManager->get('Magento\Install\Model\Installer\Pear')->getPackages();
            $params['success_callback'] = array($this, 'installSuccessCallback');
            $params['failure_callback'] = array($this, 'installFailureCallback');
        }
        $pear->runHtmlConsole($params);
        $this->getResponse()->clearAllHeaders();
    }

    /**
     * Install success callback
     */
    public function installSuccessCallback()
    {
        echo 'parent.installSuccess()';
    }

    /**
     * Install failure callback
     */
    public function installFailureCallback()
    {
        echo 'parent.installFailure()';
    }

    /**
     * Download manual action
     */
    public function downloadManualAction()
    {
        $step = $this->_getWizard()->getStepByName('download');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }

    /**
     * Configuration data installation
     */
    public function configAction()
    {
        $this->_checkIfInstalled();
        $this->_getInstaller()->checkServer();

        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);

        $data = $this->getRequest()->getQuery('config');
        if ($data) {
            $this->_session->setLocaleData($data);
        }

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getLayout()->addBlock('Magento\Install\Block\Config', 'install.config', 'content');

        $this->_view->renderLayout();
    }

    /**
     * Process configuration POST data
     */
    public function configPostAction()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('config');

        $config             = $this->getRequest()->getPost('config');
        $connectionConfig   = $this->getRequest()->getPost('connection');

        if ($config && $connectionConfig && isset($connectionConfig[$config['db_model']])) {

            $data = array_merge($config, $connectionConfig[$config['db_model']]);

            $this->_session
                ->setConfigData($data)
                ->setSkipUrlValidation($this->getRequest()->getPost('skip_url_validation'))
                ->setSkipBaseUrlValidation($this->getRequest()->getPost('skip_base_url_validation'));
            try {
                $this->_getInstaller()->installConfig($data);
                return $this->_redirect('*/*/installDb');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->getResponse()->setRedirect($step->getUrl());
            }
        }
        $this->getResponse()->setRedirect($step->getUrl());
    }

    /**
     * Install DB
     */
    public function installDbAction()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('config');
        try {
            $this->_getInstaller()->installDb();
            /**
             * Clear session config data
             */
            $this->_session->getConfigData(true);

            $this->_storeManager->getStore()->resetConfig();
            $this->_dbUpdater->updateData();

            $this->getResponse()->setRedirect($step->getNextUrl());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->getResponse()->setRedirect($step->getUrl());
        }
    }

    /**
     * Install administrator account
     */
    public function administratorAction()
    {
        $this->_checkIfInstalled();

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();

        $this->_view->getLayout()->addBlock('Magento\Install\Block\Admin', 'install.administrator', 'content');
        $this->_view->renderLayout();
    }

    /**
     * Process administrator installation POST data
     */
    public function administratorPostAction()
    {
        $this->_checkIfInstalled();

        $step = $this->_wizard->getStepByName('administrator');
        $adminData      = $this->getRequest()->getPost('admin');
        $encryptionKey  = $this->getRequest()->getPost('encryption_key');

        try {
            $encryptionKey = $this->_getInstaller()->getValidEncryptionKey($encryptionKey);
            $this->_getInstaller()->createAdministrator($adminData);
            $this->_getInstaller()->installEncryptionKey($encryptionKey);
            $this->getResponse()->setRedirect($step->getNextUrl());
        } catch (\Exception $e) {
            $this->_session->setAdminData($adminData);
            if ($e instanceof \Magento\Core\Exception) {
                $this->messageManager->addMessages($e->getMessages());
            } else {
                $this->messageManager->addError($e->getMessage());
            }
            $this->getResponse()->setRedirect($step->getUrl());
        }
    }

    /**
     * End installation
     */
    public function endAction()
    {
        $this->_checkIfInstalled();

        if ($this->_appState->isInstalled()) {
            $this->_redirect('*/*');
            return;
        }

        $this->_getInstaller()->finish();

        $this->_objectManager->get('Magento\AdminNotification\Model\Survey')->saveSurveyViewed(true);

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();

        $this->_view->getLayout()->addBlock('Magento\Install\Block\End', 'install.end', 'content');
        $this->_view->renderLayout();
        $this->_session->clearStorage();
    }
}
