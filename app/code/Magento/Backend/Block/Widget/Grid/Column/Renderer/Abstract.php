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
 * Backend grid item abstract renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

abstract class Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
    extends Magento_Backend_Block_Abstract implements Magento_Backend_Block_Widget_Grid_Column_Renderer_Interface
{
    protected $_defaultWidth;
    protected $_column;

    public function setColumn($column)
    {
        $this->_column = $column;
        return $this;
    }

    /** @return Magento_Backend_Block_Widget_Grid_Column */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        if ($this->getColumn()->getEditable()) {
            $value = $this->_getValue($row);
            return $value
                   . ($this->getColumn()->getEditOnly() ? '' : ($value != '' ? '' : '&nbsp;'))
                   . $this->_getInputValueElement($row);
        }
        return $this->_getValue($row);
    }

    /**
     * Render column for export
     *
     * @param Magento_Object $row
     * @return string
     */
    public function renderExport(Magento_Object $row)
    {
        return $this->render($row);
    }

    protected function _getValue(Magento_Object $row)
    {
        if ($getter = $this->getColumn()->getGetter()) {
            if (is_string($getter)) {
                return $row->$getter();
            } elseif (is_callable($getter)) {
                return call_user_func($getter, $row);
            }
            return '';
        }
        return $row->getData($this->getColumn()->getIndex());
    }

    public function _getInputValueElement(Magento_Object $row)
    {
        return  '<input type="text" class="input-text '
                . $this->getColumn()->getValidateClass()
                . '" name="' . $this->getColumn()->getId()
                . '" value="' . $this->_getInputValue($row) . '"/>';
    }

    protected function _getInputValue(Magento_Object $row)
    {
        return $this->_getValue($row);
    }

    public function renderHeader()
    {
        if (false !== $this->getColumn()->getSortable()) {
            $className = 'not-sort';
            $dir = strtolower($this->getColumn()->getDir());
            $nDir= ($dir=='asc') ? 'desc' : 'asc';
            if ($this->getColumn()->getDir()) {
                $className = 'sort-arrow-' . $dir;
            }
            $out = '<a href="#" name="' . $this->getColumn()->getId() . '" title="' . $nDir
                . '" class="' . $className . '">'.'<label class="sort-title" for='.$this->getColumn()->getHtmlId()
                .'>'
                . $this->getColumn()->getHeader().'</label></a>';
        } else {
            $out = '<label for='.$this->getColumn()->getHtmlId().'>'
                .$this->getColumn()->getHeader()
                .'</label>';
        }
        return $out;
    }

    public function renderProperty()
    {
        $out = '';
        $width = $this->_defaultWidth;

        if ($this->getColumn()->hasData('width')) {
            $customWidth = $this->getColumn()->getData('width');
            if ((null === $customWidth) || (preg_match('/^[0-9]+%?$/', $customWidth))) {
                $width = $customWidth;
            } elseif (preg_match('/^([0-9]+)px$/', $customWidth, $matches)) {
                $width = (int)$matches[1];
            }
        }

        if (null !== $width) {
            $out .= ' width="' . $width . '"';
        }

        return $out;
    }

    public function renderCss()
    {
        return $this->getColumn()->getCssClass();
    }

}
