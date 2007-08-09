<?php
/**
 * Adminhtml all tags
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Tag extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _init()
    {
        $this->_block = 'tag_product';
        $this->_addButtonLabel = __('Add New Tag');
        $this->_headerText = __('Tagged Products');
    }

}
