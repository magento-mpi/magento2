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
 *
 * @category    Mage
 * @package     Mage_Core
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Backend_Block_Widget_Grid_ColumnSet extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * @var Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
     */
    protected $_rowUrlGenerator;

    /**
     * Column headers visibility
     *
     * @var boolean
     */
    protected $_headersVisibility = true;

    /**
     * Filter visibility
     *
     * @var boolean
     */
    protected $_filterVisibility = true;

    /**
     * Empty grid text
     *
     * @var string|null
     */
    protected $_emptyText;

    /****
     * Empty grid text CSS class
     *
     * @var string|null
     */
    protected $_emptyTextCss    = 'a-center';

    /**
     * Label for empty cell
     *
     * @var string
     */
    protected $_emptyCellLabel = '';

    /**
     * Count subtotals
     *
     * @var boolean
     */
    protected $_countSubTotals = false;

    /**
     * SubTotals
     *
     * @var array
     */
    protected $_subtotals = array();

    /**
     * Columns to group by
     *
     * @var array
     */
    protected $_groupedColumn = array();

    /*
     * @var boolean
     */
    protected $_isCollapsed;

    /**
     * Path to template file in theme
     *
     * @var string
     */
    protected $_template = 'Mage_Backend::widget/grid/column_set.phtml';

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Backend_Helper_Data $helper,
        Mage_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory $generatorFactory,
        array $data = array()
    ) {
        $this->_helper = $helper;

        $generatorClassName = 'Mage_Backend_Model_Widget_Grid_Row_UrlGenerator';
        if (isset($data['rowUrl'])) {
            $rowUrlParams = $data['rowUrl'];
            if (isset($rowUrlParams['generatorClass'])) {
                $generatorClassName = $rowUrlParams['generatorClass'];
            }
            $this->_rowUrlGenerator
                = $generatorFactory->createUrlGenerator($generatorClassName, array('args' => $rowUrlParams));
        }

        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data);
        $this->setEmptyText($this->_helper->__('No records found.'));
    }

    /**
     * Retrieve the list of columns
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = $this->getLayout()->getChildBlocks($this->getNameInLayout());
        foreach ($columns as $key => $column) {
            if (!$column->isDisplayed()) {
                unset($columns[$key]);
            }
        }
        return $columns;
    }

    /**
     * Count columns
     *
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->getColumns());
    }

    /**
     * Set sortability flag for columns
     *
     * @param bool $value
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function setSortable($value)
    {
        if ($value === false) {
            foreach ($this->getColumns() as $column) {
                $column->setSortable(false);
            }
        }
        return $this;
    }

    /**
     * Set custom renderer type for columns
     *
     * @param string $type
     * @param string $className
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function setRendererType($type, $className)
    {
        foreach ($this->getColumns() as $column) {
            $column->setRendererType($type, $className);
        }
        return $this;
    }

    /**
     * Set custom filter type for columns
     *
     * @param string $type
     * @param string $className
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function setFilterType($type, $className)
    {
        foreach ($this->getColumns() as $column) {
            $column->setFilterType($type, $className);
        }
        return $this;
    }

    /**
     * Prepare block for rendering
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $columns = $this->getColumns();
        foreach ($columns as $columnId => $column) {
            $column->setId($columnId);
            $column->setGrid($this->getGrid());
        }
        $last = array_pop($columns);
        if ($last) {
            $last->addHeaderCssClass('last');
        }
    }

    /**
     * Return row url for js event handlers
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        $url = '#';
        if (null !== $this->_rowUrlGenerator) {
            $url = $this->_rowUrlGenerator->getUrl($item);
        }
        return $url;
    }

    /**
     * Get children of specified item
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getMultipleRows($item)
    {
        return $item->getChildren();
    }

    /**
     * Retrieve columns for multiple rows
     * @return array
     */
    public function getMultipleRowColumns()
    {
        $columns = $this->getColumns();
        foreach ($this->_groupedColumn as $column) {
            unset($columns[$column]);
        }
        return $columns;
    }

    /**
     * Check whether subtotal should be rendered
     *
     * @param Varien_Object $item
     * @return boolean
     */
    public function shouldRenderSubTotal($item)
    {
        return ($this->getCountSubTotals() &&
            count($this->getSubTotals()) > 0 &&
            count($this->getMultipleRows($item)) > 0
        );
    }

    /**
     * Retrieve rowspan number
     *
     * @param Varien_Object $item
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return integer|boolean
     */
    public function getRowspan($item, $column)
    {
        if ($this->isColumnGrouped($column)) {
            return count($this->getMultipleRows($item)) + count($this->_groupedColumn);
        }
        return false;
    }

    /**
     * Check whether given column is grouped
     *
     * @param string|object $column
     * @param string $value
     * @return boolean|Mage_Backend_Block_Widget_Grid
     */
    public function isColumnGrouped($column, $value = null)
    {
        if (null === $value) {
            if (is_object($column)) {
                return in_array($column->getIndex(), $this->_groupedColumn);
            }
            return in_array($column, $this->_groupedColumn);
        }
        $this->_groupedColumn[] = $column;
        return $this;
    }

    /**
     * Check whether should render empty cell
     *
     * @param Varien_Object $item
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return boolean
     */
    public function shouldRenderEmptyCell($item, $column)
    {
        return ($item->getIsEmpty() && in_array($column['index'], $this->_groupedColumn));
    }

    /**
     * Retrieve colspan for empty cell
     *
     * @return int
     */
    public function getEmptyCellColspan()
    {
        return $this->getColumnCount() - count($this->_groupedColumn);
    }

    /**
     * Retrieve subtotal item
     *        $this->getUrl()
     * @param Varien_Object $item
     * @return Varien_Object
     */
    public function getSubTotalItem($item)
    {
        foreach ($this->getSubTotals() as $subtotalItem) {
            foreach ($this->_groupedColumn as $groupedColumn) {
                if ($subtotalItem->getData($groupedColumn) == $item->getData($groupedColumn)) {
                    return $subtotalItem;
                }
            }
        }
        return '';
    }

    /**
     * Retrieve columns to render
     *
     * @return array
     */
    public function getSubTotalColumns()
    {
        return $this->getColumns();
    }

    /**
     * Check whether should render cell
     *
     * @param Varien_Object $item
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return boolean
     */
    public function shouldRenderCell($item, $column)
    {
        if ($this->isColumnGrouped($column) && $item->getIsEmpty()) {
            return true;
        }
        if (!$item->getIsEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * Set visibility of column headers
     *
     * @param boolean $visible
     */
    public function setHeadersVisibility($visible = true)
    {
        $this->_headersVisibility = $visible;
    }

    /**
     * Return visibility of column headers
     *
     * @return boolean
     */
    public function isHeaderVisible()
    {
        return $this->_headersVisibility;
    }

    /**
     * Set visibility of filter
     *
     * @param boolean $visible
     */
    public function setFilterVisibility($visible = true)
    {
        $this->_filterVisibility = $visible;
    }

    /**
     * Return visibility of filter
     *
     * @return boolean
     */
    public function isFilterVisible()
    {
        return $this->_filterVisibility;
    }

    /**
     * Set empty text CSS class
     *
     * @param string $cssClass
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setEmptyTextClass($cssClass)
    {
        $this->_emptyTextCss = $cssClass;
        return $this;
    }

    /**
     * Return empty text CSS class
     *
     * @return string
     */
    public function getEmptyTextClass()
    {
        return $this->_emptyTextCss;
    }

    /**
     * Retrieve label for empty cell
     *
     * @return string
     */
    public function getEmptyCellLabel()
    {
        return $this->_emptyCellLabel;
    }

    /**
     * Set label for empty cell
     *
     * @param string $label
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setEmptyCellLabel($label)
    {
        $this->_emptyCellLabel = $label;
        return $this;
    }

    /**
     * Set flag whether is collapsed
     * @param $isCollapsed
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function setIsCollapsed($isCollapsed)
    {
        $this->_isCollapsed = $isCollapsed;
        return $this;
    }

    /**
     * Retrieve flag is collapsed
     * @return mixed
     */
    public function getIsCollapsed()
    {
        return $this->_isCollapsed;
    }

    /**
     * Return grid of current column set
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function getGrid()
    {
        return $this->getParentBlock();
    }

    /**
     * Return collection of current grid
     * @return Varien_Data_Collection
     */
    public function getCollection()
    {
        return $this->getGrid()->getCollection();
    }

    /**
     * Set subtotals
     *
     * @param boolean $flag
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setCountSubTotals($flag = true)
    {
        $this->_countSubTotals = $flag;
        return $this;
    }

    /**
     * Return count subtotals
     *
     * @return mixed
     */
    public function getCountSubTotals()
    {
        return $this->_countSubTotals;
    }

    /**
     * Set subtotal items
     *
     * @param array $items
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setSubTotals(array $items)
    {
        $this->_subtotals = $items;
        return $this;
    }

    /**
     * Retrieve subtotal items
     *
     * @return array
     */
    public function getSubTotals()
    {
        return $this->_subtotals;
    }
}
