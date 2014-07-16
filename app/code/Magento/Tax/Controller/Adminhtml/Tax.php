<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml common tax class controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Controller\Adminhtml;

class Tax extends \Magento\Backend\App\Action
{

    /**
     * Validate/Filter Tax Class Type
     *
     * @param string $classType
     * @return string processed class type
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _processClassType($classType)
    {
        $validClassTypes = array(
            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER,
            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
        );
        if (!in_array($classType, $validClassTypes)) {
            throw new \Magento\Framework\Model\Exception(__('Invalid type of tax class specified.'));
        }
        return $classType;
    }

    /**
     * Validate/Filter Tax Class Name
     *
     * @param string $className
     * @return string processed class name
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _processClassName($className)
    {
        $className = trim($this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($className));
        if ($className == '') {
            throw new \Magento\Framework\Model\Exception(__('Invalid name of tax class specified.'));
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
