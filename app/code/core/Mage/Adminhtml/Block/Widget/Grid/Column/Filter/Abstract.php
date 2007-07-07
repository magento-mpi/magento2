<?php
/**
 * Grid colum filter block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract extends Mage_Core_Block_Abstract implements Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Interface
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
    
    protected function _getHtmlName()
    {
        return $this->getColumn()->getId();//$this->getColumn()->getGrid()->getVarNameFilter().'['.$this->getColumn()->getId().']';
    }
    
    protected function _getHtmlId()
    {
        return $this->getColumn()->getGrid()->getVarNameFilter().'_'.$this->getColumn()->getId();
    }
    
    public function getEscapedValue($index=null)
    {
        return htmlspecialchars($this->getValue($index));
    }
    
    public function getCondition()
    {
        return array('like'=>'%'.$this->getValue().'%');
    }
    
    public function getHtml()
    {
        return '';
    }
}
