<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Block_Widget_Abstract extends Magento_Core_Block_Template
{
    /**
     * Customer address
     *
     * @var Magento_Customer_Helper_Address
     */
    protected $_customerAddress = null;

    /**
     * @param Magento_Customer_Helper_Address $customerAddress
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Helper_Address $customerAddress,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerAddress = $customerAddress;
        parent::__construct($coreData, $context, $data);
    }

    public function getConfig($key)
    {
        return $this->_customerAddress->getConfig($key);
    }

    public function getFieldIdFormat()
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', '%s');
        }
        return $this->getData('field_id_format');
    }

    public function getFieldNameFormat()
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', '%s');
        }
        return $this->getData('field_name_format');
    }

    public function getFieldId($field)
    {
        return sprintf($this->getFieldIdFormat(), $field);
    }

    public function getFieldName($field)
    {
        return sprintf($this->getFieldNameFormat(), $field);
    }

    /**
     * Retrieve customer attribute instance
     *
     * @param string $attributeCode
     * @return Magento_Customer_Model_Attribute|false
     */
    protected function _getAttribute($attributeCode)
    {
        return Mage::getSingleton('Magento_Eav_Model_Config')->getAttribute('customer', $attributeCode);
    }
}
