<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Type_Date
    extends Magento_Adminhtml_Block_Widget_Form
{

    protected $_template = 'edit/type/date.phtml';

    /**
     * Select element for choosing attribute type
     *
     * @return string
     */
    public function getDateFormatSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
            ->setData(array(
                'id'    =>  '{{prefix}}_attribute_{{id}}_date_format',
                'class' => 'select global-scope'
            ))
            ->setName('attributes[{{prefix}}][{{id}}][date_format]')
            ->setOptions($this->getDateFormatOptions());

        return $select->getHtml();
    }

    /**
     * Return array of date formats
     *
     * @return array
     */
    public function getDateFormatOptions()
    {
         return array(
            array(
                'value' => Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT,
                'label' => __('Short')
            ),
            array(
                'value' => Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM,
                'label' => __('Medium')
            ),
            array(
                'value' => Magento_Core_Model_LocaleInterface::FORMAT_TYPE_LONG,
                'label' => __('Long')
            ),
            array(
                'value' => Magento_Core_Model_LocaleInterface::FORMAT_TYPE_FULL,
                'label' => __('Full')
            )
        );
    }
}
