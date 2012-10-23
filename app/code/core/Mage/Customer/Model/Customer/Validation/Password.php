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
class Mage_Customer_Model_Customer_Validation_Password extends Magento_Validator_ValidatorAbstract
{
    const MIN_PASSWORD_LENGTH = 6;

    /**
     * @var array
     */
    protected $_messages = array();

    /**
     * Validate password fields
     *
     * @param Varien_Object $value
     * @return boolean
     */
    public function isValid($value)
    {
        $password = $value->getData('password');
        if (!empty($password)) {
            if (!$this->_validatePassword($value, 'password')) {
                return false;
            }
        }

        $newPassword = $value->getData('new_password');
        if (!empty($newPassword)) {
            if (!$this->_validatePassword($value, 'new_password')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate password
     *
     * @param Varien_Object $value
     * @param string $passwordFieldName
     * @return bool
     */
    public function _validatePassword($value, $passwordFieldName)
    {
        $password = $value->getData($passwordFieldName);

        $notEmptyValidator = new Magento_Validator_NotEmpty();
        if (!$value->getId() && !$notEmptyValidator->isValid($password)) {
            $this->_addErrorMessage('password',
                Mage::helper('Mage_Customer_Helper_Data')->__('The password cannot be empty.'));
            return false;
        }

        $strlenValidator = new Magento_Validator_StringLength();
        $strlenValidator->setMin(self::MIN_PASSWORD_LENGTH);
        if (!$strlenValidator->isValid($password)) {
            $this->_addErrorMessage('password',
                Mage::helper('Mage_Customer_Helper_Data')->
                    __('The minimum password length is %s.', self::MIN_PASSWORD_LENGTH));
            return false;
        }

        $confirmation = $value->getConfirmation();
        if ($password != $confirmation) {
            $this->_addErrorMessage('password',
                Mage::helper('Mage_Customer_Helper_Data')->__('Please make sure your passwords match.'));
            return false;
        }

        return true;
    }

    /**
     * Add error messages
     *
     * @param string $code
     * @param string $message
     */
    protected function _addErrorMessage($code, $message)
    {
        if (isset($this->_messages[$code])) {
            $this->_messages[$code][] = $message;
        } else {
            $this->_messages[$code] = array($message);
        }

    }

    /**
     * Get validation messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
