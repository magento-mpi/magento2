<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Integration\Controller\Adminhtml\Integration;

use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Exception as IntegrationException;

class Save extends \Magento\Integration\Controller\Adminhtml\Integration
{
    /**
     * Redirect merchant to 'Edit integration' or 'New integration' if error happened during integration save.
     *
     * @return void
     */
    protected function _redirectOnSaveError()
    {
        $integrationId = $this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
        if ($integrationId) {
            $this->_redirect('*/*/edit', ['id' => $integrationId]);
        } else {
            $this->_redirect('*/*/new');
        }
    }

    /**
     * Save integration action.
     *
     * @return void
     * @todo: Fix cyclomatic complexity.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var array $integrationData */
        $integrationData = [];
        try {
            $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
            if ($integrationId) {
                try {
                    $integrationData = $this->_integrationService->get($integrationId)->getData();
                } catch (IntegrationException $e) {
                    $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->_logger->logException($e);
                    $this->messageManager->addError(__('Internal error. Check exception log for details.'));
                    $this->_redirect('*/*');
                    return;
                }
            }
            /** @var array $data */
            $data = $this->getRequest()->getPost();
            if (!empty($data)) {
                // TODO: Move out work with API permissions to Web API module
                if (!isset($data['resource'])) {
                    $integrationData['resource'] = [];
                }
                $integrationData = array_merge($integrationData, $data);
                if (!isset($integrationData[Info::DATA_ID])) {
                    $integration = $this->_integrationService->create($integrationData);
                } else {
                    $integration = $this->_integrationService->update($integrationData);
                }
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    $this->messageManager->addSuccess(
                        __(
                            'The integration \'%1\' has been saved.',
                            $this->escaper->escapeHtml($integration->getName())
                        )
                    );
                }
                if ($this->getRequest()->isXmlHttpRequest()) {
                    $isTokenExchange = $integration->getEndpoint() && $integration->getIdentityLinkUrl() ? '1' : '0';
                    $this->getResponse()->representJson(
                        $this->_coreHelper->jsonEncode(
                            ['integrationId' => $integration->getId(), 'isTokenExchange' => $isTokenExchange]
                        )
                    );
                } else {
                    $this->_redirect('*/*/');
                }
            } else {
                $this->messageManager->addError(__('The integration was not saved.'));
            }
        } catch (IntegrationException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            $this->_getSession()->setIntegrationData($integrationData);
            $this->_redirectOnSaveError();
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            $this->_redirectOnSaveError();
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            $this->_redirectOnSaveError();
        }
    }
}
