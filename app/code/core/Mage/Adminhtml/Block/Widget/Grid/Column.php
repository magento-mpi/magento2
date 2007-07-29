<?php
/**
 * Grid column block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column extends Mage_Adminhtml_Block_Widget
{
    protected $_grid;
    protected $_renderer;
    protected $_filter;
    protected $_type;
    protected $_cssClass;

    public function __construct($data=array())
    {
        parent::__construct($data);
    }

    public function setGrid($grid)
    {
        $this->_grid = $grid;
        // Init filter object
        $this->getFilter();
        return $this;
    }

    public function getGrid()
    {
        return $this->_grid;
    }
    
    public function isLast()
    {
        return $this->getId() == $this->getGrid()->getLastColumnId();
    }

    public function getHtmlProperty()
    {
        return $this->getRenderer()->renderProperty();
    }

    public function getHeaderHtml()
    {
        return $this->getRenderer()->renderHeader();
    }

    public function getCssClass()
    {
        if (!$this->_cssClass) {
            if ($this->getAlign()) {
                $this->_cssClass = 'a-'.$this->getAlign();
            }
        }
        return $this->_cssClass;
    }

    public function getHeaderCssClass()
    {
        $class = $this->getData('header_css_class');
        if ($this->getSortable()===false) {
            $class.= ' no-link';
        }
        if ($this->isLast()) {
            $class = ' last';
        }
        return $class;
    }
    
    public function getHeaderHtmlProperty()
    {
        $str = '';
        if ($class = $this->getHeaderCssClass()) {
            $str.= ' class="'.$class.'"';
        }
        if ($this->getEditable()) {
            $str.= ' colspan="2"';
        }
        
        return $str;
    }

    /**
     * Retrieve row column field value for display
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function getRowField(Varien_Object $row)
    {
        return $this->getRenderer()->render($row);
    }

    public function setRenderer($renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    protected function _getRendererByType()
    {
        switch (strtolower($this->getType())) {
            case 'date':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_date';
                break;
            case 'datetime':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_datetime';
                break;
            case 'currency':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_currency';
                break;
            case 'concat':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_concat';
                break;
            case 'action':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_action';
                break;
            case 'boolean':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_boolean';
                break;
            case 'options':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_options';
                break;
            case 'checkbox':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_checkbox';
                break;
            default:
                $rendererClass = 'adminhtml/widget_grid_column_renderer_text';
                break;
        }
        return $rendererClass;
    }

    public function getRenderer()
    {
        if (!$this->_renderer) {
            $rendererClass = $this->getData('renderer');
            if (!$rendererClass) {
                $rendererClass = $this->_getRendererByType();
            }
            $this->_renderer = $this->getLayout()->createBlock($rendererClass)
                ->setColumn($this);
        }
        return $this->_renderer;
    }

    public function setFilter($column)
    {
    }

    protected function _getFilterByType()
    {
        switch (strtolower($this->getType())) {
            case 'datetime':
                $filterClass = 'adminhtml/widget_grid_column_filter_datetime';
                break;
            case 'date':
                $filterClass = 'adminhtml/widget_grid_column_filter_date';
                break;
            case 'number':
            case 'currency':
                $filterClass = 'adminhtml/widget_grid_column_filter_range';
                break;
            case 'options':
                $filterClass = 'adminhtml/widget_grid_column_filter_select';
                break;
            case 'checkbox':
                $filterClass = 'adminhtml/widget_grid_column_filter_checkbox';
                break;
            default:
                $filterClass = 'adminhtml/widget_grid_column_filter_text';
                break;
        }
        return $filterClass;
    }

    public function getFilter()
    {
        if (!$this->_filter) {
            $filterClass = $this->getData('filter');
            if ($filterClass === false) {
                return false;
            }
            if (!$filterClass) {
                $filterClass = $this->_getFilterByType();
            }
            $this->_filter = $this->getLayout()->createBlock($filterClass)
                ->setColumn($this);
        }

        return $this->_filter;
    }

    public function getFilterHtml()
    {
        if ($this->getFilter()) {
            return $this->getFilter()->getHtml();
        } else {
            return '<div style="width: 100%;">&nbsp;</div>';
        }
        return null;
    }
}