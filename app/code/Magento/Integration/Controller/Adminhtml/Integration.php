<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Controller\Adminhtml;

/**
 * Controller for integrations management.
 */
class Integration extends \Magento\Adminhtml\Controller\Action
{
    /** Param Key for extracting integration id from Request */
    const PARAM_INTEGRATION_ID = 'id';

    /**#@+
     * Data keys for extracting information from Integration data array.
     */
    const DATA_INTEGRATION_ID = 'integration_id';
    const DATA_NAME = 'name';
    /**#@-*/

    /** Keys used for registering data into the registry */
    const REGISTRY_KEY_CURRENT_INTEGRATION = 'current_integration';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /** @var \Magento\Integration\Service\IntegrationV1Interface */
    private $_integrationService;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Integration\Service\IntegrationV1Interface $integrationService
     * @param \Magento\Core\Model\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Integration\Service\IntegrationV1Interface $integrationService,
        \Magento\Core\Model\Registry $registry
    ) {
        $this->_registry = $registry;
        $this->_integrationService = $integrationService;
        parent::__construct($context);
    }

    /**
     * Integrations grid.
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Integration::system_integrations');
        $this->_addBreadcrumb(
            __('Integrations'),
            __('Integrations')
        );
        $this->_title(__('Integrations'));
        $this->renderLayout();
    }

    /**
     * AJAX integrations grid.
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
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

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
        if ($integrationId) {
            $integrationData = $this->_integrationService->get($integrationId);
            if (!$integrationData[self::DATA_INTEGRATION_ID]) {
                $this->_getSession()
                    ->addError(__('This integration no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if (!$this->_registry->registry(self::REGISTRY_KEY_CURRENT_INTEGRATION)) {
                $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }


    /**
     * Save integration action
     */
    public function saveAction()
    {
        try {
            $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
            /** @var array $integrationData */
            $integrationData = array();
            if ($integrationId) {
                $integrationData = $this->_integrationService->get($integrationId);
                if (!$integrationData[self::DATA_INTEGRATION_ID]) {
                    $this->_getSession()->addError(__('This integration no longer exists.'));
                    $this->_redirect('*/*/');
                    return;
                }
            }
            /** @var array $data */
            $data = $this->getRequest()->getPost();
            //Merge Post-ed data
            $integrationData = array_merge($integrationData, $data);
            $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
            if (!$integrationData[self::DATA_INTEGRATION_ID]) {
                $this->_integrationService->create($integrationData);
            } else {
                $this->_integrationService->update($integrationData);
            }
            $this->_getSession()->addSuccess(
                __(
                    'The integration \'%1\' has been saved.',
                    $integrationData[self::DATA_NAME]
                )
            );
            $this->_redirect('*/*/');
        } catch (\Magento\Integration\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect(
                '*/*/edit',
                array('id' => $this->getRequest()->getParam(self::PARAM_INTEGRATION_ID))
            );
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }
}