<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form weight field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight extends Magento_Data_Form_Element_Text
{
    const VIRTUAL_FIELD_HTML_ID = 'weight_and_type_switcher';

    /**
     * Is virtual checkbox element
     *
     * @var Magento_Data_Form_Element_Checkbox
     */
    protected $_virtual;

    /**
     * Catalog helper
     *
     * @var Magento_Catalog_Helper_Product
     */
    protected $_helper;

    /**
     * @param Magento_Catalog_Helper_Product $helper
     * @param Magento_Data_Form_Element_CheckboxFactory $elementFactory
     * @param array $attributes
     */
    public function __construct(
        Magento_Catalog_Helper_Product $helper,
        Magento_Data_Form_Element_CheckboxFactory $elementFactory,
        array $attributes = array()
    ) {
        $this->_helper = $helper;
        $this->_virtual = $elementFactory->create();
        $this->_virtual->setId(self::VIRTUAL_FIELD_HTML_ID)->setName('is_virtual')
            ->setLabel($this->_helper->getTypeSwitcherControlLabel());
        $data['class'] = 'validate-number validate-zero-or-greater validate-number-range number-range-0-99999999.9999';
        parent::__construct($attributes);
    }

    /**
     * Add Is Virtual checkbox html to weight field
     *
     * @return string
     */
    public function getElementHtml()
    {
        if (!$this->getForm()->getDataObject()->getTypeInstance()->hasWeight()) {
            $this->_virtual->setChecked('checked');
        }
        return '<div class="fields-group-2"><div class="field"><div class="addon"><div class="control">'
            . parent::getElementHtml()
            . '<label class="addafter" for="'
            . $this->getHtmlId()
            . '"><strong>'. __('lbs') .'</strong></label>'
            . '</div></div></div><div class="field choice">'
            . $this->_virtual->getElementHtml() . $this->_virtual->getLabelHtml()
            . '</div></div>';
    }

    /**
     * Set form for both fields
     *
     * @param Magento_Data_Form $form
     * @return Magento_Data_Form
     */
    public function setForm($form)
    {
        $this->_virtual->setForm($form);
        return parent::setForm($form);
    }
}
