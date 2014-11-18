<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Tax;

class AjaxDelete extends \Magento\Tax\Controller\Adminhtml\Tax
{
    /**
     * Delete Tax Class via AJAX
     *
     * @return void
     */
    public function execute()
    {
        $classId = (int)$this->getRequest()->getParam('class_id');
        try {
            $this->taxClassRepository->deleteByIdentifier($classId);
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
}
