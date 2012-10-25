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
    const VIRTUAL_FIELD_HTML_ID = 'weight_is_virtual';

    /**
     * Is virtual checkbox element
     *
     * @var Varien_Data_Form_Element_Checkbox
     */
    protected $_virtual;

    /**
     * Types available for transition
     *
     * @var array
     */
    protected $_transisionalTypes = array(
        'simple' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        'virtual' => Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
        'downloadable' => Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
    );

    public function __construct(array $data = array())
    {
        $this->_virtual = $this->_createVirtualElement();
        parent::__construct($data);
    }

    public function getElementHtml()
    {
        if ($this->getForm()->getDataObject()->getTypeId() !== Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
            && $this->_isTransitionalType()) {
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

    protected function _createVirtualElement()
    {
        $element = Mage::getModel('Varien_Data_Form_Element_Checkbox');
        $element->setId(self::VIRTUAL_FIELD_HTML_ID)->setName('is_virtual');
        return $element;
    }

    protected function _isTransitionalType()
    {
        return in_array($this->getForm()->getDataObject()->getTypeId(), $this->_transisionalTypes);
    }
}
