<?php
/**
 * Adminhtml grid item renderer interface
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
interface Mage_Adminhtml_Block_Widget_Grid_Renderer_Interface extends Zend_Filter_Interface
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row 
     * @param string $index 
     * @param string $format 
     */
    public function render(Varien_Object $row, $index, $format=null);
}