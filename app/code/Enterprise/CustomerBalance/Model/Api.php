<?php
/**
 * Store credit API
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_CustomerBalance_Model_Api extends Magento_Api_Model_Resource_Abstract
{
    /**
     * Retrieve customer store credit balance information
     *
     * @param  string $customerId
     * @param  string $websiteId
     * @return float
     */
    public function balance($customerId, $websiteId)
    {
        /**
         * @var Enterprise_CustomerBalance_Model_Balance $balanceModel
         */
        try {
            $balanceModel = Mage::getModel('Enterprise_CustomerBalance_Model_Balance')
                    ->setCustomerId($customerId)
                    ->setWebsiteId($websiteId)
                    ->loadByCustomer();
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        // check if balance found
        if (!$balanceModel->getId()) {
            $this->_fault('balance_not_found');
        }
        return $balanceModel->getAmount();
    }

    /**
     * Retrieve customer store credit history information
     *
     * @param  string $customerId
     * @param  string|null $websiteId
     * @return array
     */
    public function history($customerId, $websiteId = null)
    {
        try {
            $result = Mage::getModel('Enterprise_CustomerBalance_Model_Balance_History')
                    ->getHistoryData($customerId, $websiteId);
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        // check if history found
        if (empty($result)) {
            $this->_fault('history_not_found');
        }
        return $result;
    }

}
