<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ValidationVatNumber
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ValidationVatNumber_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Clicking button "Validate VAT Number" and confirm popup message
     *
     * @param array|string $testData
     * @param array|string $userAddressData
     * @param string $messageType
     */
    public function validationVatMessages($testData, $userAddressData, $messageType)
    {
        $this->addParameter('currentCustomerGroup', $testData['customerGroups']['group_default']);
        switch ($messageType) {
            case 'validIntraunionMessage':
                $this->addParameter('newCustomerGroup', $testData['customerGroups']['group_valid_vat_intraunion']);
                $this->clickButtonAndConfirm('billing_validate_vat_number', 'valid_domestic_group_message', false);
                $this->verifyForm(array('customer_group' => $testData['customerGroups']['group_valid_vat_intraunion']));
                break;
            case 'validDomesticMessage':
                $this->addParameter('newCustomerGroup', $testData['customerGroups']['group_valid_vat_domestic']);
                $this->clickButtonAndConfirm('billing_validate_vat_number', 'valid_domestic_group_message', false);
                $this->verifyForm(array('customer_group' => $testData['customerGroups']['group_valid_vat_domestic']));
                break;
            case 'invalidMessage':
                $this->addParameter('vatNumber', $userAddressData['billing_vat_number']);
                $this->addParameter('newCustomerGroup', $testData['customerGroups']['group_invalid_vat']);
                $this->clickButtonAndConfirm('billing_validate_vat_number', 'invalid_vat_id_message', false);
                $this->verifyForm(array('customer_group' => $testData['customerGroups']['group_invalid_vat']));
                break;
            default:
                $this->fail('Incorrect message type');
        }
    }
}
