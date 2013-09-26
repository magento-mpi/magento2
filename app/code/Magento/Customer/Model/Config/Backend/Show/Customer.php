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
 * Customer Show Customer Model
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Config_Backend_Show_Customer extends Magento_Core_Model_Config_Value
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
     * Retrieve attribute code
     *
     * @return string
     */
    protected function _getAttributeCode()
    {
        return str_replace('_show', '', $this->getField());
    }

    /**
     * Retrieve attribute objects
     *
     * @return array
     */
    protected function _getAttributeObjects()
    {
        return array(
            $this->_eavConfig->getAttribute('customer', $this->_getAttributeCode())
        );
    }

    /**
     * Actions after save
     *
     * @return Magento_Customer_Model_Config_Backend_Show_Customer
     */
    protected function _afterSave()
    {
        $result = parent::_afterSave();

        $valueConfig = array(
            ''    => array('is_required' => 0, 'is_visible' => 0),
            'opt' => array('is_required' => 0, 'is_visible' => 1),
            '1'   => array('is_required' => 0, 'is_visible' => 1),
            'req' => array('is_required' => 1, 'is_visible' => 1),
        );

        $value = $this->getValue();
        if (isset($valueConfig[$value])) {
            $data = $valueConfig[$value];
        } else {
            $data = $valueConfig[''];
        }

        if ($this->getScope() == 'websites') {
            $website = $this->_storeManager->getWebsite($this->getWebsiteCode());
            $dataFieldPrefix = 'scope_';
        } else {
            $website = null;
            $dataFieldPrefix = '';
        }

        foreach ($this->_getAttributeObjects() as $attributeObject) {
            if ($website) {
                $attributeObject->setWebsite($website);
                $attributeObject->load($attributeObject->getId());
            }
            $attributeObject->setData($dataFieldPrefix . 'is_required', $data['is_required']);
            $attributeObject->setData($dataFieldPrefix . 'is_visible',  $data['is_visible']);
            $attributeObject->save();
        }

        return $result;
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
            $website = $this->_storeManager->getWebsite($this->getWebsiteCode());
            foreach ($this->_getAttributeObjects() as $attributeObject) {
                $attributeObject->setWebsite($website);
                $attributeObject->load($attributeObject->getId());
                $attributeObject->setData('scope_is_required', null);
                $attributeObject->setData('scope_is_visible',  null);
                $attributeObject->save();
            }
        }

        return $result;
    }
}
