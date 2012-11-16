<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend system config array field renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_System_Config_Form_Field_Regexceptions
    extends Mage_Backend_Block_System_Config_Form_Field_Array_Abstract
{
    protected function _construct()
    {
        $this->addColumn('search', array(
            'label' => $this->helper('Mage_Backend_Helper_Data')->__('Search String'),
            'style' => 'width:120px',
        ));
        $this->addColumn('value', array(
            'label' => $this->helper('Mage_Backend_Helper_Data')->__('Design Theme'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add Exception');
        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if ($columnName == 'value' && isset($this->_columns[$columnName])) {
            $element = new Varien_Data_Form_Element_Select();
            $element
                ->setForm($this->getForm())
                ->setName($this->_getCellInputElementName($columnName))
                ->setHtmlId($this->_getCellInputElementId('#{_id}', $columnName))
                ->setValues(Mage::getModel('Mage_Core_Model_Theme')->getLabelsCollection(false));
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::_renderCellTemplate($columnName);
    }
}
