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
 * VAT validation controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Customer\System\Config;

class Validatevat extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Perform customer VAT ID validation
     *
     * @return \Magento\Object
     */
    protected function _validate()
    {
        return $this->_objectManager->get('Magento\Customer\Helper\Data')
            ->checkVatNumber(
                $this->getRequest()->getParam('country'),
                $this->getRequest()->getParam('vat')
            );
    }

    /**
     * Check whether vat is valid
     *
     * @return void
     */
    public function validateAction()
    {
        $result = $this->_validate();
        $this->getResponse()->setBody((int)$result->getIsValid());
    }

    /**
     * Retrieve validation result as JSON
     *
     * @return void
     */
    public function validateAdvancedAction()
    {
        /** @var $coreHelper \Magento\Core\Helper\Data */
        $coreHelper = $this->_objectManager->get('Magento\Core\Helper\Data');

        $result = $this->_validate();
        $valid = $result->getIsValid();
        $success = $result->getRequestSuccess();
        // ID of the store where order is placed
        $storeId = $this->getRequest()->getParam('store_id');
        // Sanitize value if needed
        if (!is_null($storeId)) {
            $storeId = (int)$storeId;
        }

        $groupId = $this->_objectManager->get('Magento\Customer\Helper\Data')
            ->getCustomerGroupIdBasedOnVatNumber(
                $this->getRequest()->getParam('country'), $result, $storeId
            );

        $body = $coreHelper->jsonEncode(array(
            'valid' => $valid,
            'group' => $groupId,
            'success' => $success
        ));
        $this->getResponse()->setBody($body);
    }
}
