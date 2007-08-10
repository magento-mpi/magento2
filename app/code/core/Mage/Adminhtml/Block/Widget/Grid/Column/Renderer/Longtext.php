<?php
/**
 * Adminhtml Grid Renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Longtext extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $maxLenght = ( $this->getColumn()->getStringLimit() ) ? $this->getColumn()->getStringLimit() : 250;
        $text = parent::_getValue($row);
        $suffix = ( $this->getColumn()->getSuffix() ) ? $this->getColumn()->getSuffix() : '...';

        if( strlen($text) > $maxLenght ) {
            return substr($text, 0, $maxLenght) . $suffix;
        } else {
            return $text;
        }
    }
}