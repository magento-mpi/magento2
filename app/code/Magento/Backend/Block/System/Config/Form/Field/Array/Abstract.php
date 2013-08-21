<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend system config array field renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Backend_Block_System_Config_Form_Field_Array_Abstract
    extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = array();

    /**
     * Enable the "Add after" button or not
     *
     * @var bool
     */
    protected $_addAfter = true;

    /**
     * Label of add button
     *
     * @var string
     */
    protected $_addButtonLabel;

    /**
     * Rows cache
     *
     * @var array|null
     */
    private $_arrayRowsCache;

    /**
     * Indication whether block is prepared to render or no
     *
     * @var bool
     */
    protected $_isPreparedToRender = false;

    protected $_template = 'Magento_Backend::system/config/form/field/array.phtml';

    /**
     * Check if columns are defined, set template
     *
     */
    protected function _construct()
    {
        if (!$this->_addButtonLabel) {
            $this->_addButtonLabel = __('Add');
        }
        parent::_construct();
        
    }

    /**
     * Add a column to array-grid
     *
     * @param string $name
     * @param array $params
     */
    public function addColumn($name, $params)
    {
        $this->_columns[$name] = array(
            'label'     => $this->_getParam($params, 'label', 'Column'),
            'size'      => $this->_getParam($params, 'size', false),
            'style'     => $this->_getParam($params, 'style'),
            'class'     => $this->_getParam($params, 'class'),
            'renderer'  => false,
        );
        if ((!empty($params['renderer'])) && ($params['renderer'] instanceof Magento_Core_Block_Abstract)) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }

    /**
     * Retrieve param
     *
     * @param array $params
     * @param string $paramName
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function _getParam($params, $paramName, $defaultValue = null)
    {
        return empty($params[$paramName]) ? $defaultValue : $params[$paramName];
    }

    /**
     * Get the grid and scripts contents
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $html = $this->_toHtml();
        $this->_arrayRowsCache = null; // doh, the object is used as singleton!
        return $html;
    }

    /**
     * Prepare existing row data object
     *
     * @param Magento_Object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _prepareArrayRow(Magento_Object $row)
    {
        // override in descendants
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
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }
        $result = array();
        /** @var Magento_Data_Form_Element_Abstract */
        $element = $this->getElement();
        if ($element->getValue() && is_array($element->getValue())) {
            foreach ($element->getValue() as $rowId => $row) {
                $rowColumnValues = array();
                foreach ($row as $key => $value) {
                    $row[$key] = $this->escapeHtml($value);
                    $rowColumnValues[$this->_getCellInputElementId($rowId, $key)] = $row[$key];
                }
                $row['_id'] = $rowId;
                $row['column_values'] = $rowColumnValues;
                $result[$rowId] = new Magento_Object($row);
                $this->_prepareArrayRow($result[$rowId]);
            }
        }
        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }

    /**
     * Get name for cell element
     *
     * @param string $rowId
     * @param string $columnName
     * @return string
     */
    protected function _getCellInputElementId($rowId, $columnName)
    {
        return $rowId . '_' . $columnName;
    }

    /**
     * Get id for cell element
     *
     * @param string $columnName
     * @return string
     */
    protected function _getCellInputElementName($columnName)
    {
        return $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->_getCellInputElementName($columnName);

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)
                ->setInputId($this->_getCellInputElementId('#{_id}', $columnName))
                ->setColumnName($columnName)
                ->setColumn($column)
                ->toHtml();
        }

        return '<input type="text" id="' . $this->_getCellInputElementId('#{_id}', $columnName) .'"' .
            ' name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        // Override in descendants to add columns, change add button label etc
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isPreparedToRender) {
            $this->_prepareToRender();
            $this->_isPreparedToRender = true;
        }
        if (empty($this->_columns)) {
            throw new Exception('At least one column must be defined.');
        }
        return parent::_toHtml();
    }

    /**
     * Returns true if the addAfter directive is set
     *
     * @return bool
     */
    public function isAddAfter()
    {
        return $this->_addAfter;
    }

    /**
     * Returns columns array
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }
}
