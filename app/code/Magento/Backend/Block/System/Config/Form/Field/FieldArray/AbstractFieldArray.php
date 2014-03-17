<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Config\Form\Field\FieldArray;

/**
 * Backend system config array field renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractFieldArray extends \Magento\Backend\Block\System\Config\Form\Field
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

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::system/config/form/field/array.phtml';

    /**
     * Check if columns are defined, set template
     *
     * @return void
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
     * @return void
     */
    public function addColumn($name, $params)
    {
        $this->_columns[$name] = array(
            'label' => $this->_getParam($params, 'label', 'Column'),
            'size' => $this->_getParam($params, 'size', false),
            'style' => $this->_getParam($params, 'style'),
            'class' => $this->_getParam($params, 'class'),
            'renderer' => false
        );
        if (!empty($params['renderer']) && $params['renderer'] instanceof \Magento\View\Element\AbstractBlock) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }

    /**
     * Retrieve param
     *
     * @param array $params
     * @param string $paramName
     * @param string|bool $defaultValue
     * @return string|bool
     */
    protected function _getParam($params, $paramName, $defaultValue = null)
    {
        return empty($params[$paramName]) ? $defaultValue : $params[$paramName];
    }

    /**
     * Get the grid and scripts contents
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        $html = $this->_toHtml();
        $this->_arrayRowsCache = null;
        // doh, the object is used as singleton!
        return $html;
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Object $row
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _prepareArrayRow(\Magento\Object $row)
    {
    }

    /**
     * Obtain existing data from form element
     *
     * Each row will be instance of \Magento\Object
     *
     * @return array
     */
    public function getArrayRows()
    {
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }
        $result = array();
        /** @var \Magento\Data\Form\Element\AbstractElement */
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
                $result[$rowId] = new \Magento\Object($row);
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
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);

        if ($column['renderer']) {
            return $column['renderer']->setInputName(
                $inputName
            )->setInputId(
                $this->_getCellInputElementId('#{_id}', $columnName)
            )->setColumnName(
                $columnName
            )->setColumn(
                $column
            )->toHtml();
        }

        return '<input type="text" id="' . $this->_getCellInputElementId(
            '#{_id}',
            $columnName
        ) .
            '"' .
            ' name="' .
            $inputName .
            '" value="#{' .
            $columnName .
            '}" ' .
            ($column['size'] ? 'size="' .
            $column['size'] .
            '"' : '') .
            ' class="' .
            (isset(
            $column['class']
        ) ? $column['class'] : 'input-text') . '"' . (isset(
            $column['style']
        ) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
    }

    /**
     * Render block HTML
     *
     * @return string
     * @throws \Exception
     */
    protected function _toHtml()
    {
        if (!$this->_isPreparedToRender) {
            $this->_prepareToRender();
            $this->_isPreparedToRender = true;
        }
        if (empty($this->_columns)) {
            throw new \Exception('At least one column must be defined.');
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
