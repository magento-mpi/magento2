<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Exception as IntegrationException;
use Magento\Integration\Helper\Oauth\Consumer as OauthConsumerHelper;
use Magento\Integration\Model\Integration as IntegrationModel;

/**
 * Controller for integrations management.
 */
class Integration extends Action
{
    /** Param Key for extracting integration id from Request */
    const PARAM_INTEGRATION_ID = 'id';

    /** Keys used for registering data into the registry */
    const REGISTRY_KEY_CURRENT_INTEGRATION = 'current_integration';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /** @var \Magento\Logger */
    protected $_logger;

    /** @var \Magento\Integration\Service\IntegrationV1Interface */
    private $_integrationService;

    /** @var OauthConsumerHelper */
    protected $_oauthConsumerHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Integration\Service\IntegrationV1Interface $integrationService
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Logger $logger
     * @param OauthConsumerHelper $oauthConsumerHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Integration\Service\IntegrationV1Interface $integrationService,
        \Magento\Core\Model\Registry $registry,
        \Magento\Logger $logger,
        OauthConsumerHelper $oauthConsumerHelper
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->_logger = $logger;
        $this->_integrationService = $integrationService;
        $this->_oauthConsumerHelper = $oauthConsumerHelper;
    }

    /**
     * Integrations grid.
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Integration::system_integrations');
        $this->_addBreadcrumb(__('Integrations'), __('Integrations'));
        $this->_title->add(__('Integrations'));
        $this->_view->renderLayout();
    }

    /**
     * AJAX integrations grid.
     */
    public function gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Check ACL.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Integration::integrations');
    }

    /**
     * New integration action.
     */
    public function newAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Integration::system_integrations');
        $this->_addBreadcrumb(__('New Integration'), __('New Integration'));
        $this->_title->add(__('New Integration'));
        /** Try to recover integration data from session if it was added during previous request which failed. */
        $restoredIntegration = $this->_getSession()->getIntegrationData();
        if ($restoredIntegration) {
            $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $restoredIntegration);
            $this->_getSession()->setIntegrationData(array());
        }
        $this->_view->renderLayout();
    }

    /**
     * Edit integration action.
     */
    public function editAction()
    {
        /** Try to recover integration data from session if it was added during previous request which failed. */
        $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
        if ($integrationId) {
            try {
                $integrationData = $this->_integrationService->get($integrationId);
                $originalName = $integrationData[Info::DATA_NAME];
            } catch (IntegrationException $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->_logger->logException($e);
                $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
                $this->_redirect('*/*');
                return;
            }
            $restoredIntegration = $this->_getSession()->getIntegrationData();
            if (isset($restoredIntegration[Info::DATA_ID]) && $integrationId == $restoredIntegration[Info::DATA_ID]) {
                $integrationData = array_merge($integrationData, $restoredIntegration);
            }

            if (isset($integrationData[Info::DATA_SETUP_TYPE])
                && $integrationData[Info::DATA_SETUP_TYPE] == IntegrationModel::TYPE_CONFIG
            ) {
                //Cannot edit Integrations created from Config. Show read only forms

            }

        } else {
            $this->_getSession()->addError(__('Integration ID is not specified or is invalid.'));
            $this->_redirect('*/*/');
            return;
        }
        $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
        $this->_view->loadLayout();
        $this->_getSession()->setIntegrationData(array());
        $this->_setActiveMenu('Magento_Integration::system_integrations');
        $this->_addBreadcrumb(
            __('Edit "%1" Integration', $integrationData[Info::DATA_NAME]),
            __('Edit "%1" Integration', $integrationData[Info::DATA_NAME])
        );
        $this->_title->add(__('Edit "%1" Integration', $originalName));
        $this->_view->renderLayout();
    }

    /**
     * Save integration action.
     *
     * TODO: Fix cyclomatic complexity.
     */
    public function saveAction()
    {
        /** @var array $integrationData */
        $integrationData = array();
        try {
            $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
            if ($integrationId) {
                try {
                    $integrationData = $this->_integrationService->get($integrationId);
                } catch (IntegrationException $e) {
                    $this->_getSession()->addError($e->getMessage());
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->_logger->logException($e);
                    $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
                    $this->_redirect('*/*');
                    return;
                }
            }
            /** @var array $data */
            $data = $this->getRequest()->getPost();
            if (!empty($data)) {
                if (!isset($data['resource'])) {
                    $integrationData['resource'] = array();
                }
                $integrationData = array_merge($integrationData, $data);
                if (!isset($integrationData[Info::DATA_ID])) {
                    $this->_integrationService->create($integrationData);
                } else {
                    $this->_integrationService->update($integrationData);
                }
                $this->_getSession()
                    ->addSuccess(__('The integration \'%1\' has been saved.', $integrationData[Info::DATA_NAME]));
            } else {
                $this->_getSession()->addError(__('The integration was not saved.'));
            }
            $this->_redirect('*/*/');
        } catch (\Magento\Integration\Exception $e) {
            $this->_getSession()->addError($e->getMessage())->setIntegrationData($integrationData);
            $this->_redirectOnSaveError();
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectOnSaveError();
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectOnSaveError();
        }
    }

    /**
     * Show permissions popup.
     */
    public function permissionsDialogAction()
    {
        $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);

        if ($integrationId) {
            try {
                $integrationData = $this->_integrationService->get($integrationId);
                $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
            } catch (IntegrationException $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->_logger->logException($e);
                $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
                $this->_redirect('*/*');
                return;
            }
        } else {
            $this->_getSession()->addError(__('Integration ID is not specified or is invalid.'));
            $this->_redirect('*/*/');
            return;
        }

        /** Add handles of the tabs which are defined in other modules */
        $handleNodes = $this->_view->getLayout()->getUpdate()->getFileLayoutUpdatesXml()
            ->xpath('//referenceBlock[@name="integration.activate.permissions.tabs"]/../@id');
        $handles = array();
        if (is_array($handleNodes)) {
            foreach ($handleNodes as $node) {
                $handles[] = (string)$node;
            }
        }
        $this->_view->loadLayout($handles);
        $this->_view->renderLayout();
    }

    /**
     * Show tokens popup.
     */
    public function tokensDialogAction()
    {
        try {
            $integrationId = $this->getRequest()->getParam('id');
            $integrationData = $this->_integrationService->get($integrationId);
            $this->_oauthConsumerHelper->createAccessToken($integrationData[IntegrationModel::CONSUMER_ID]);
            $this->_registry->register(
                self::REGISTRY_KEY_CURRENT_INTEGRATION,
                $this->_integrationService->get($integrationId)
            );
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*');
            return;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
            $this->_redirect('*/*');
            return;
        }
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Redirect merchant to 'Edit integration' or 'New integration' if error happened during integration save.
     */
    protected function _redirectOnSaveError()
    {
        $integrationId = $this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
        if ($integrationId) {
            $this->_redirect('*/*/edit', array('id' => $integrationId));
        } else {
            $this->_redirect('*/*/new');
        }
    }
}
