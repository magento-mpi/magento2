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
class Mage_Customer_Model_Customer_Validation_Password implements Magento_Validator_ValidatorInterface
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
            if (!$this->_validatePassword($value, $password)) {
                return false;
            }
        }

        $newPassword = $value->getData('new_password');
        if (!empty($newPassword)) {
            if (!$this->_validatePassword($value, $newPassword)) {
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
     */
    public function _validatePassword($value, $passwordFieldName)
    {
        $password = $value->getData($passwordFieldName);

        if (!$value->getId() && !Zend_Validate::is($password, 'NotEmpty')) {
            $this->_addErrorMessage('password',
                Mage::helper('Mage_Customer_Helper_Data')->__('The password cannot be empty.'));
            return false;
        }

        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(self::MIN_PASSWORD_LENGTH))) {
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
        $this->_messages[$code][] = $message;
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
