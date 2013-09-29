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
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Eav_Model_Config $eavConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Eav_Model_Config $eavConfig,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        parent::__construct($coreData, $context, $data);
    }

    public function getConfig($key)
    {
        return $this->helper('Magento_Customer_Helper_Address')->getConfig($key);
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
        return $this->_eavConfig->getAttribute('customer', $attributeCode);
    }
}
