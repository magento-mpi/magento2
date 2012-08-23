<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer date field form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form_Renderer_Date extends Enterprise_Eav_Block_Form_Renderer_Date
{
    /**
     * Field type
     *
     * @var string
     */
    protected $_filedType = 'date';

    /**
     * Prepare values for renderer
     *
     * @return array
     */
    protected function _prepareValues()
    {
        return array('day' => array(
            'id' => $this->getHtmlId('day'),
            'title' => $this->__('Day'),
            'label' => $this->__('DD'),
            'value' => $this->getDay()
        ), 'month' => array(
            'id' => $this->getHtmlId('month'),
            'title' => $this->__('Month'),
            'label' => $this->__('MM'),
            'value' => $this->getMonth()
        ), 'year' => array(
            'id' => $this->getHtmlId('year'),
            'title' => $this->__('Year'),
            'label' => $this->__('YYYY'),
            'value' => $this->getYear()
        ));
    }

    /**
     * Add date field to fieldset xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_Date
     */
    public function addFieldToXmlObj(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj)
    {
        $attributes = array(
            'label' => $this->getLabel(),
            'name'  => $this->getFieldName(),
            'date_format'=> $this->getDateFormat(),
            'value' => $this->_prepareValues()
        );

        $attributes += Mage::helper('Mage_XmlConnect_Helper_Customer_Form_Renderer')
            ->addTitleAndRequiredAttr($fieldsetXmlObj, $this);
        $fieldXmlObj = $fieldsetXmlObj->addField($this->getHtmlId('full'), $this->_filedType, $attributes);
        $validateRules = $this->getAttributeObject()->getValidateRules();

        if (!empty($validateRules)) {
            $validatorXmlObj = $fieldXmlObj->addValidator();
            if (!empty($validateRules['input_validation'])) {
                $validatorXmlObj->addRule(array('type' => $validateRules['input_validation']));
            }
        }

        return $this;
    }
}
