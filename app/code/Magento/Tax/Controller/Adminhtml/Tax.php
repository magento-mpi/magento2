<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Controller\Adminhtml;

use Magento\Framework\Exception\InputException;

/**
 * Adminhtml common tax class controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Tax\Service\V1\TaxClassServiceInterface
     */
    private $taxClassService;

    /**
     * @var \Magento\Tax\Service\V1\Data\TaxClassBuilder
     */
    private $taxClassBuilder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService
     * @param \Magento\Tax\Service\V1\Data\TaxClassBuilder $taxClassBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService,
        \Magento\Tax\Service\V1\Data\TaxClassBuilder $taxClassBuilder
    ) {
        $this->taxClassService = $taxClassService;
        $this->taxClassBuilder = $taxClassBuilder;
        parent::__construct($context);
    }

    /**
     * Save Tax Class via AJAX
     *
     * @return void
     */
    public function ajaxSaveAction()
    {
        try {
            $taxClassId = (int)$this->getRequest()->getPost('class_id') ?: null;

            $taxClass = $this->taxClassBuilder
                ->setClassType((string)$this->getRequest()->getPost('class_type'))
                ->setClassName($this->_processClassName((string)$this->getRequest()->getPost('class_name')))
                ->create();
            if ($taxClassId) {
                $this->taxClassService->updateTaxClass($taxClassId, $taxClass);
            } else {
                $taxClassId = $this->taxClassService->createTaxClass($taxClass);
            }

            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array(
                    'success' => true,
                    'error_message' => '',
                    'class_id' => $taxClassId,
                    'class_name' => $taxClass->getClassName()
                )
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $responseContent = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(
                array('success' => false, 'error_message' => $e->getMessage(), 'class_id' => '', 'class_name' => '')
            );
        } catch (\Exception $e) {
            $responseContent = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(
                array(
                    'success' => false,
                    'error_message' => __('Something went wrong saving this tax class.'),
                    'class_id' => '',
                    'class_name' => ''
                )
            );
        }
        $this->getResponse()->representJson($responseContent);
    }

    /**
     * Delete Tax Class via AJAX
     *
     * @return void
     */
    public function ajaxDeleteAction()
    {
        $classId = (int)$this->getRequest()->getParam('class_id');
        try {
            $this->taxClassService->deleteTaxClass($classId);
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => true, 'error_message' => '')
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => false, 'error_message' => $e->getMessage())
            );
        } catch (\Exception $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => false, 'error_message' => __('Something went wrong deleting this tax class.'))
            );
        }
        $this->getResponse()->representJson($responseContent);
    }

    /**
     * Validate/Filter Tax Class Name
     *
     * @param string $className
     * @return string processed class name
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function _processClassName($className)
    {
        $className = trim($this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($className));
        if ($className == '') {
            throw new InputException('Invalid name of tax class specified.');
        }
        return $className;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Tax::manage_tax');
    }

    /**
     * Set tax ignore notification flag and redirect back
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function ignoreTaxNotificationAction()
    {
        $section = $this->getRequest()->getParam('section');
        if ($section) {
            try {
                $path = 'tax/notification/ignore_' . $section;
                $this->_objectManager->get('\Magento\Core\Model\Resource\Config')->saveConfig($path, 1, \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, 0);
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }
}
