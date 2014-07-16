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
            /** @var $classModel \Magento\Tax\Model\ClassModel */
            $classModel = $this->_objectManager->create('Magento\Tax\Model\ClassModel')->load($classId);
            $classModel->delete();
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => true, 'error_message' => '')
            );
        } catch (\Magento\Framework\Model\Exception $e) {
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
