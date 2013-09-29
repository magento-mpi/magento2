<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Address Street Model
 *
 * @method string getWebsiteCode
 */
class Magento_Customer_Model_Config_Backend_Address_Street extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Actions after save
     *
     * @return Magento_Customer_Model_Config_Backend_Address_Street
     */
    protected function _afterSave()
    {
        $attribute = $this->_eavConfig->getAttribute('customer_address', 'street');
        $value  = $this->getValue();
        switch ($this->getScope()) {
            case 'websites':
                $website = $this->_storeManager->getWebsite($this->getWebsiteCode());
                $attribute->setWebsite($website);
                $attribute->load($attribute->getId());
                if ($attribute->getData('multiline_count') != $value) {
                    $attribute->setData('scope_multiline_count', $value);
                }
                break;

            case 'default':
                $attribute->setData('multiline_count', $value);
                break;
        }
        $attribute->save();
        return $this;
    }

    /**
     * Processing object after delete data
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _afterDelete()
    {
        $result = parent::_afterDelete();

        if ($this->getScope() == 'websites') {
            $attribute = $this->_eavConfig->getAttribute('customer_address', 'street');
            $website = $this->_storeManager->getWebsite($this->getWebsiteCode());
            $attribute->setWebsite($website);
            $attribute->load($attribute->getId());
            $attribute->setData('scope_multiline_count', null);
            $attribute->save();
        }

        return $result;
    }
}
