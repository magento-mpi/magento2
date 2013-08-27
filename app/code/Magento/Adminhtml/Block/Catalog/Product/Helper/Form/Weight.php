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

    public function __construct(array $data = array())
    {
        $this->_helper = isset($data['helper']) ? $data['helper'] : $this->_helper;
        $this->_virtual = isset($data['element'])
            ? $data['element']
            : Mage::getModel('Magento_Data_Form_Element_Checkbox');
        $this->_virtual->setId(self::VIRTUAL_FIELD_HTML_ID)->setName('is_virtual')
            ->setLabel($this->_helper->getTypeSwitcherControlLabel());
        $data['class'] = 'validate-number validate-zero-or-greater validate-number-range number-range-0-99999999.9999';
        parent::__construct($data);
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
