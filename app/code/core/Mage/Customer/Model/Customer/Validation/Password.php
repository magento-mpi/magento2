<?php
/**
 * {license_notice}
 *
 * @category  Mage
 * @package   Mage_Customer
 * @copyright {copyright}
 * @license   {license_link}
 */

/**
 * Customer password field validation constraint.
 *
 * @category Mage
 * @package  Mage_Customer
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Validation_Password extends Magento_Validator_ConstraintAbstract
{
    /**
     * Validate password field
     *
     * @param array $data
     * @param null $field
     * @return bool|void
     */
    public function isValidData(array $data, $field = null)
    {
        $isValid = true;
        if (!isset($data['password']) && !isset($data['new_password'])) {
            /** @var Mage_Customer_Helper_Data $helper */
            $helper = Mage::getModel('Mage_Customer_Helper_Data');
            $this->addError('password', $helper->__('The password field is not present in request.'));
            $isValid = false;
        }

        return $isValid;
    }
}
