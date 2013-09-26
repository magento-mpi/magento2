<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config data model
 *
 * @method Magento_Core_Model_Resource_Config_Data _getResource()
 * @method Magento_Core_Model_Resource_Config_Data getResource()
 * @method string getScope()
 * @method Magento_Core_Model_Config_Value setScope(string $value)
 * @method int getScopeId()
 * @method Magento_Core_Model_Config_Value setScopeId(int $value)
 * @method string getPath()
 * @method Magento_Core_Model_Config_Value setPath(string $value)
 * @method string getValue()
 * @method Magento_Core_Model_Config_Value setValue(string $value)
 *
 * @category    Mage
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Magento_Core_Model_Config_Value extends Magento_Core_Model_Abstract
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

    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Magento model constructor
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Resource_Config_Data');
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
            return $this->_storeManager->getStore($storeCode)->getConfig($path);
        }
        if ($websiteCode) {
            return $this->_storeManager->getWebsite($websiteCode)->getConfig($path);
        }
        return (string) $this->_config->getValue($path, 'default');
    }


    /**
     * Get value by key for new user data from <section>/groups/<group>/fields/<field>
     *
     * @param string $key
     * @return string
     */
    public function getFieldsetDataValue($key)
    {
        $data = $this->_getData('fieldset_data');
        return (is_array($data) && isset($data[$key])) ? $data[$key] : null;
    }
}
