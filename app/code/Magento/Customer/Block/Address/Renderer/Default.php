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
 * Address format renderer default
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Block_Address_Renderer_Default
    extends Magento_Core_Block_Abstract
    implements Magento_Customer_Block_Address_Renderer_Interface
{
    /**
     * Format type object
     *
     * @var Magento_Object
     */
    protected $_type;

    /**
     * Retrive format type object
     *
     * @return Magento_Object
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Retrive format type object
     *
     * @param  Magento_Object $type
     * @return Magento_Customer_Model_Address_Renderer_Default
     */
    public function setType(Magento_Object $type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getFormat(Magento_Customer_Model_Address_Abstract $address=null)
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
     * @param Magento_Customer_Model_Address_Abstract $address
     * @return string
     */
    public function render(Magento_Customer_Model_Address_Abstract $address, $format=null)
    {
        switch ($this->getType()->getCode()) {
            case 'html':
                $dataFormat = Magento_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML;
                break;
            case 'pdf':
                $dataFormat = Magento_Customer_Model_Attribute_Data::OUTPUT_FORMAT_PDF;
                break;
            case 'oneline':
                $dataFormat = Magento_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ONELINE;
                break;
            default:
                $dataFormat = Magento_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT;
                break;
        }

        $formater   = new Magento_Filter_Template();
        $attributes = Mage::helper('Magento_Customer_Helper_Address')->getAttributes();

        $data = array();
        foreach ($attributes as $attribute) {
            /* @var $attribute Magento_Customer_Model_Attribute */
            if (!$attribute->getIsVisible()) {
                continue;
            }
            if ($attribute->getAttributeCode() == 'country_id') {
                $data['country'] = $address->getCountryModel()->getName();
            } else if ($attribute->getAttributeCode() == 'region') {
                $data['region'] = __($address->getRegion());
            } else {
                $dataModel = Magento_Customer_Model_Attribute_Data::factory($attribute, $address);
                $value     = $dataModel->outputValue($dataFormat);
                if ($attribute->getFrontendInput() == 'multiline') {
                    $values    = $dataModel->outputValue(Magento_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ARRAY);
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
