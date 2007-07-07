<?php
/**
 * Adminhtml grid item renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Block extends Varien_Filter_Object implements Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface
{
    public function render(Varien_Object $row)
    {
        $block->setPageObject($row);
        echo $block->toHtml();
    }
}