<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Address format renderer default
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Block_Address_Renderer_Default
    extends Mage_Core_Block_Abstract
    implements Mage_Customer_Block_Address_Renderer_Interface
{
    /**
     * Format type object
     *
     * @var Varien_Object
     */
    protected $_type;

    /**
     * Retrive format type object
     *
     * @return Varien_Object
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Retrive format type object
     *
     * @param  Varien_Object $type
     * @return Mage_Customer_Model_Address_Renderer_Default
     */
    public function setType(Varien_Object $type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getFormat(Mage_Customer_Model_Address_Abstract $address=null)
    {
        $countryFormat = is_null($address)
            ? false
            : $address->getCountryModel()->getFormat($this->getType()->getCode());
        $format = $countryFormat ? $countryFormat->getFormat() : $this->getType()->getDefaultFormat();
        return $format;
    }

    /**
     * Render address
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return string
     */
    public function render(Mage_Customer_Model_Address_Abstract $address, $format=null)
    {
        switch ($this->getType()->getCode()) {
            case 'html':
                $dataFormat = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML;
                break;
            case 'pdf':
                $dataFormat = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_PDF;
                break;
            case 'oneline':
                $dataFormat = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ONELINE;
                break;
            default:
                $dataFormat = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT;
                break;
        }

        $formater   = new Varien_Filter_Template();
        $attributes = Mage::helper('Mage_Customer_Helper_Address')->getAttributes();

        $data = array();
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            if (!$attribute->getIsVisible()) {
                continue;
            }
            if ($attribute->getAttributeCode() == 'country_id') {
                $data['country'] = $address->getCountryModel()->getName();
            } else if ($attribute->getAttributeCode() == 'region') {
                $data['region'] = Mage::helper('Mage_Directory_Helper_Data')->__($address->getRegion());
            } else {
                $dataModel = Mage_Customer_Model_Attribute_Data::factory($attribute, $address);
                $value     = $dataModel->outputValue($dataFormat);
                if ($attribute->getFrontendInput() == 'multiline') {
                    $values    = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ARRAY);
                    // explode lines
                    foreach ($values as $k => $v) {
                        $key = sprintf('%s%d', $attribute->getAttributeCode(), $k + 1);
                        $data[$key] = $v;
                    }
                }
                $data[$attribute->getAttributeCode()] = $value;
            }
        }

        if ($this->getType()->getEscapeHtml()) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escapeHtml($value);
            }
        }

        $formater->setVariables($data);

        $format = !is_null($format) ? $format : $this->getFormat($address);

        return $formater->filter($format);
    }
}
