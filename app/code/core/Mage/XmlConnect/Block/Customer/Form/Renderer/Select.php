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
 * Customer select field form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form_Renderer_Select extends Enterprise_Customer_Block_Form_Renderer_Select
{
    /**
     * Field type
     *
     * @var string
     */
    protected $_filedType = 'select';

    /**
     * Add select field to fieldset xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_Select
     */
    public function addFieldToXmlObj(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj)
    {
        $attributes = array(
            'label' => $this->getLabel(),
            'name'  => $this->getFieldName(),
            'value' => $this->getValue(),
            'options' => $this->getOptions()
        );

        $attributes += Mage::helper('Mage_XmlConnect_Helper_Customer_Form_Renderer')
            ->addTitleAndRequiredAttr($fieldsetXmlObj, $this);

        $fieldsetXmlObj->addField($this->getHtmlId(), $this->_filedType, $attributes);

        return $this;
    }
}
