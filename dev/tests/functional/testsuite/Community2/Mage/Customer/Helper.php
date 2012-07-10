<?php
# Magento
#
# {license_notice}
#
# @category    Magento
# @package     Mage_AdminUser
# @subpackage  functional_tests
# @copyright   {copyright}
# @license     {license_link}
#

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Customer_Helper extends Core_Mage_Customer_Helper
{
    /**
     * Verify that address is present.
     * PreConditions: Customer is opened on 'Addresses' tab.
     *
     * @param array $addressData
     *
     * @return int|mixed|string
     */
    public function isAddressPresent(array $addressData)
    {
        $xpath = $this->_getControlXpath('fieldset', 'list_customer_addresses') . '//li';
        $addressCount = $this->getXpathCount($xpath);
        for ($i = $addressCount; $i > 0; $i--) {
            $this->click($xpath . "[$i]");
            $id = $this->getValue($xpath . "[$i]/@id");
            $arrayId = explode('_', $id);
            $id = end($arrayId);
            $this->addParameter('address_number', $id);
            if ($this->verifyForm($addressData, 'addresses')) {
                $this->clearMessages();
                return $id;
            }
        }
        return 0;
    }

    /**
     * Check if customer is present in customers grid
     *
     * @param array $userData
     * @return bool
     */
    public function isCustomerPresentInGrid($userData)
    {
        $data = array('email' => $userData['email']);
        $this->_prepareDataForSearch($data);
        $xpathTR = $this->search($data, 'customers_grid');
        if (!is_null($xpathTR)) {
            return true;
        } else {
            return false;
        }
    }
}