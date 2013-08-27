<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity registrants data model
 *
 * @method Enterprise_GiftRegistry_Model_Resource_Person _getResource()
 * @method Enterprise_GiftRegistry_Model_Resource_Person getResource()
 * @method Enterprise_GiftRegistry_Model_Person setEntityId(int $value)
 * @method string getFirstname()
 * @method Enterprise_GiftRegistry_Model_Person setFirstname(string $value)
 * @method string getLastname()
 * @method Enterprise_GiftRegistry_Model_Person setLastname(string $value)
 * @method string getEmail()
 * @method Enterprise_GiftRegistry_Model_Person setEmail(string $value)
 * @method string getRole()
 * @method Enterprise_GiftRegistry_Model_Person setRole(string $value)
 * @method string getCustomValues()
 * @method Enterprise_GiftRegistry_Model_Person setCustomValues(string $value)
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_Person extends Magento_Core_Model_Abstract
{
    function _construct() {
        $this->_init('Enterprise_GiftRegistry_Model_Resource_Person');
    }

    /**
     * Gift registry data
     *
     * @var Enterprise_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param Enterprise_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Enterprise_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($context, $resource, $resourceCollection, $data);
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
        $attributes = Mage::getSingleton('Enterprise_GiftRegistry_Model_Entity')->getRegistrantAttributes();

        $errorsCustom = $this->_giftRegistryData->validateCustomAttributes($customValues, $attributes);
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
    public function unserialiseCustom() {
        if (is_string($this->getCustomValues())) {
            $this->setCustom(unserialize($this->getCustomValues()));
        }
        return $this;
    }
}
