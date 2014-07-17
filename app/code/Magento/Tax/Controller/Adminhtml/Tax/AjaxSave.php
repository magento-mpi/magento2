<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Tax;

class AjaxSave extends \Magento\Tax\Controller\Adminhtml\Tax
{
    /**
     * Save Tax Class via AJAX
     *
     * @return void
     */
    public function execute()
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
}
