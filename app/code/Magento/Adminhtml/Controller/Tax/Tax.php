<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml common tax class controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Tax;

class Tax extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Save Tax Class via AJAX
     */
    public function ajaxSaveAction()
    {
        $responseContent = '';
        try {
            $classData = array(
                'class_id' => (int)$this->getRequest()->getPost('class_id') ?: null, // keep null for new tax classes
                'class_type' => $this->_processClassType((string)$this->getRequest()->getPost('class_type')),
                'class_name' => $this->_processClassName((string)$this->getRequest()->getPost('class_name'))
            );
            $class = \Mage::getModel('Magento\Tax\Model\ClassModel')
                ->setData($classData)
                ->save();
            $responseContent = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array(
                'success' => true,
                'error_message' => '',
                'class_id' => $class->getId(),
                'class_name' => $class->getClassName()
            ));
        } catch (\Magento\Core\Exception $e) {
            $responseContent = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array(
                'success' => false,
                'error_message' => $e->getMessage(),
                'class_id' => '',
                'class_name' => ''
            ));
        } catch (\Exception $e) {
            $responseContent = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array(
                'success' => false,
                'error_message' => __('Something went wrong saving this tax class.'),
                'class_id' => '',
                'class_name' => ''
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Delete Tax Class via AJAX
     */
    public function ajaxDeleteAction()
    {
        $classId = (int)$this->getRequest()->getParam('class_id');
        try {
            /** @var $classModel \Magento\Tax\Model\ClassModel */
            $classModel = $this->_objectManager->create('Magento\Tax\Model\ClassModel')->load($classId);
            $classModel->checkClassCanBeDeleted();
            $classModel->delete();
            $responseContent = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array(
                'success' => true,
                'error_message' => ''
            ));
        } catch (\Magento\Core\Exception $e) {
            $responseContent = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array(
                'success' => false,
                'error_message' => $e->getMessage()
            ));
        } catch (\Exception $e) {
            $responseContent = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array(
                'success' => false,
                'error_message' => __('Something went wrong deleting this tax class.')
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Validate/Filter Tax Class Type
     *
     * @param string $classType
     * @return string processed class type
     * @throws \Magento\Core\Exception
     */
    protected function _processClassType($classType)
    {
        $validClassTypes = array(
            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER,
            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
        );
        if (!in_array($classType, $validClassTypes)) {
            \Mage::throwException(__('Invalid type of tax class specified.'));
        }
        return $classType;
    }

    /**
     * Validate/Filter Tax Class Name
     *
     * @param string $className
     * @return string processed class name
     * @throws \Magento\Core\Exception
     */
    protected function _processClassName($className)
    {
        $className = trim($this->_objectManager->get('\Magento\Tax\Helper\Data')->escapeHtml($className));
        if ($className == '') {
            \Mage::throwException(__('Invalid name of tax class specified.'));
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
}
