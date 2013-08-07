<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Columns block to add new columns into the table
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Field_Backend_Columns
    extends Mage_Backend_Block_System_Config_Form_Field_Array_Abstract
    implements Mage_Widget_Block_Interface
{
    /**
     * Rows cache
     *
     * @var array|null
     */
    private $_arrayRowsCache;

    /**
     * Constructor - initialize columns
     */
    public function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::widget/items_grid/array.phtml')
            ->setIsAddAfter(false)
            ->setAddButtonLabel($this->__('Add Column'));

        $this->addColumn('option', array(
            'label' => $this->__('Option'),
            'style' => 'width:220px',
            'class' => 'select required-entry'
        ));
        $this->addColumn('title', array(
            'label' => $this->__('Title'),
            'style' => 'width:290px',
            'class' => 'input-text required-entry'
        ));
        $this->addColumn('width', array(
            'label' => $this->__('Width, px'),
            'style' => 'width:35px',
            'class' => 'validate-greater-than-zero validate-digits input-text required-entry'
        ));
        $this->addColumn('sort_order', array(
            'label' => $this->__('Sort Order'),
            'style' => 'width:35px',
            'class' => 'validate-greater-than-zero validate-digits input-text'
        ));
    }

    /**
     * Prepare columns editor html element
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return Magento_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $element->setRenderer($this);
        return $element;
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     * @throws Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception($this->__('Wrong column name specified.'));
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']
                ->setInputName($inputName)
                ->setColumnName($columnName)
                ->setColumn($column)
                ->toHtml();
        }

        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . ' items-grid-field"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    }

    /**
     * Render option select field for specified type
     *
     * @param string $type
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function renderOptionSelect($type)
    {
        $column = $this->_columns['option'];
        $inputName = $this->getElement()->getName() . '[#{_id}][property]';
        $columnSize = $column['size'] ? 'size="' . $column['size'] . '"' : '';
        $columnClass = isset($column['class']) ? $column['class'] : 'select';
        $columnStyle = isset($column['style']) ? ' style="'.$column['style'] . '"' : '';

        $html = '<select name="'. $inputName .'" id="printedtemplate_property_#{_id}"' .
            $columnSize . ' class="' . $columnClass . ' skip-submit items-grid-field"'. $columnStyle . '>';

        $options = Mage::getSingleton('Saas_PrintedTemplate_Model_Config')->getItemPropertiesArray($type);
        foreach ($options as $name => $params) {
            $html .= '<option value="' . $name . '">' . $this->__($params['label']) . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Obtain existing data from form element
     *
     * Each row will be instance of Magento_Object
     *
     * @return array
     */
    public function getArrayRows()
    {
        if (!is_null($this->_arrayRowsCache)) {
            return $this->_arrayRowsCache;
        }

        $values = $this->getElement()->getValue();
        if (empty($values)) {
            return array();
        }

        // the variable $parameters should be filled by parse_str function
        $parameters = null;
        parse_str(base64_decode($values));

        if (is_null($parameters) || !isset($parameters['columns_editor'])) {
            return array();
        }

        foreach ($parameters['columns_editor'] as $rowId => $columns) {
            $row = new Magento_Object(array('_id' => $rowId));
            foreach ($columns as $columnName => $columnValue) {
                $row->setData($columnName, $this->escapeHtml($columnValue));
            }

            $result[$rowId] = $row;
            $this->_prepareArrayRow($result[$rowId]);
        }

        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }

    /**
     * Render field
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $elementId = $element->getHtmlId();
        $html = '<tr id="row_' . $elementId . '">';
        $html .= '<td colspan="2">';

        $html .= $this->_getElementHtml($element);

        $html .= '</td></tr>';

        return $html;
    }

    /**
     * Get helper object
     *
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }
}
