<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config data model
 *
 * @method Mage_Core_Model_Resource_Config_Data _getResource()
 * @method Mage_Core_Model_Resource_Config_Data getResource()
 * @method string getScope()
 * @method Mage_Core_Model_Config_Value setScope(string $value)
 * @method int getScopeId()
 * @method Mage_Core_Model_Config_Value setScopeId(int $value)
 * @method string getPath()
 * @method Mage_Core_Model_Config_Value setPath(string $value)
 * @method string getValue()
 * @method Mage_Core_Model_Config_Value setValue(string $value)
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Mage_Core_Model_Config_Value extends Mage_Core_Model_Abstract
{
    const ENTITY = 'core_config_data';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_config_data';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'config_data';

    /**
     * Magento model constructor
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Config_Data');
    }

    /**
     * Add availability call after load as public
     */
    public function afterLoad()
    {
        $this->_afterLoad();
    }

    /**
     * Check if config data value was changed
     *
     * @return bool
     */
    public function isValueChanged()
    {
        return $this->getValue() != $this->getOldValue();
    }

    /**
     * Get old value from existing config
     *
     * @return string
     */
    public function getOldValue()
    {
        $storeCode   = $this->getStoreCode();
        $websiteCode = $this->getWebsiteCode();
        $path        = $this->getPath();

        if ($storeCode) {
            return Mage::app()->getStore($storeCode)->getConfig($path);
        }
        if ($websiteCode) {
            return Mage::app()->getWebsite($websiteCode)->getConfig($path);
        }
        return (string) Mage::getConfig()->getValue($path, 'default');
    }


    /**
     * Get value by key for new user data from <section>/groups/<group>/fields/<field>
     *
     * @return string
     */
    public function getFieldsetDataValue($key)
    {
        $data = $this->_getData('fieldset_data');
        return (is_array($data) && isset($data[$key])) ? $data[$key] : null;
    }
}
