<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form weight field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_Renderer extends Varien_Data_Form_Element_Text
{
    const VIRTUAL_FIELD_HTML_ID = 'weight_and_type_switcher';

    /**
     * Is virtual checkbox element
     *
     * @var Varien_Data_Form_Element_Checkbox
     */
    protected $_virtual;

    public function __construct(array $data = array())
    {
        $this->_virtual = isset($data['element'])
            ? $data['element']
            : Mage::getModel('Varien_Data_Form_Element_Checkbox');
        $this->_virtual->setId(self::VIRTUAL_FIELD_HTML_ID)->setName('is_virtual');
        parent::__construct($data);
    }

    /**
     * Add Is Virtual checkbox html to weight field
     *
     * @return string
     */
    public function getElementHtml()
    {
        if ($this->getForm()->getDataObject()->getTypeInstance()->isWeightDisabled()) {
            $this->_virtual->setChecked('checked');
        }
        return $this->_virtual->getElementHtml() . parent::getElementHtml();
    }

    /**
     * Set form for both fields
     *
     * @param Varien_Data_Form $form
     * @return Varien_Data_Form
     */
    public function setForm($form)
    {
        $this->_virtual->setForm($form);
        return parent::setForm($form);
    }

}
