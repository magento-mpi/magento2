<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity registrants data model
 *
 * @method Magento_GiftRegistry_Model_Resource_Person _getResource()
 * @method Magento_GiftRegistry_Model_Resource_Person getResource()
 * @method Magento_GiftRegistry_Model_Person setEntityId(int $value)
 * @method string getFirstname()
 * @method Magento_GiftRegistry_Model_Person setFirstname(string $value)
 * @method string getLastname()
 * @method Magento_GiftRegistry_Model_Person setLastname(string $value)
 * @method string getEmail()
 * @method Magento_GiftRegistry_Model_Person setEmail(string $value)
 * @method string getRole()
 * @method Magento_GiftRegistry_Model_Person setRole(string $value)
 * @method string getCustomValues()
 * @method Magento_GiftRegistry_Model_Person setCustomValues(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Model_Person extends Magento_Core_Model_Abstract
{
    function _construct()
    {
        $this->_init('Magento_GiftRegistry_Model_Resource_Person');
    }

    /**
     * Validate registrant attribute values
     *
     * @return array|bool
     */
    public function validate()
    {
        // not Checking entityId !!!
        $errors = array();

        if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors[] = __('Please enter the first name.');
        }

        if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors[] = __('Please enter the last name.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = __('Please enter a valid email address(for example, daniel@x.com).');
        }

        $customValues = $this->getCustom();
        $attributes = Mage::getSingleton('Magento_GiftRegistry_Model_Entity')->getRegistrantAttributes();

        $errorsCustom = Mage::helper('Magento_GiftRegistry_Helper_Data')->validateCustomAttributes($customValues, $attributes);
        if ($errorsCustom !== true) {
            $errors = empty($errors) ? $errorsCustom : array_merge($errors, $errorsCustom);
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Unpack "custom" value array
     *
     * @return $this
     */
    public function unserialiseCustom()
    {
        if (is_string($this->getCustomValues())) {
            $this->setCustom(unserialize($this->getCustomValues()));
        }
        return $this;
    }
}
