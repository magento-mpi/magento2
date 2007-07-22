<?php
/**
 * Adminhtml grid item abstract renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */

abstract class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract extends Mage_Core_Block_Abstract implements Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface
{
    protected $_column;

    public function setColumn($column)
    {
        $this->_column = $column;
        return $this;
    }

    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
    
    public function renderHeader()
    {
        $out = '';
        if ($this->getColumn()->getSortable()!==false) {

            $className = 'not-sort';
            $dir = (strtolower($this->getColumn()->getDir())=='asc') ? 'desc' : 'asc';
            if ($this->getColumn()->getDir()) {
                $className = 'sort-arrow-' . $dir;
            }
            $out = '<a href="" name="'.$this->getColumn()->getId().'" target="'.$dir
                   .'" class="' . $className . '"><span class="sort-title">'.$this->getColumn()->getHeader().'</span></a>';
        }
        else {
            $out = $this->getColumn()->getHeader();
        }
        return $out;        
    }
    
    public function renderProperty()
    {
        $out = ' ';
        if ($this->getColumn()->getWidth()) {
            $out .='width="'.$this->getColumn()->getWidth(). (is_numeric($this->getColumn()->getWidth()) ? '%' : '') . '" ';
        } 
        return $out;
    }
}