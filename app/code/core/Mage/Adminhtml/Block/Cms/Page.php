<?php
/**
 * Adminhtml cms pages content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Cms_Page extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'cms_page';
        $this->_headerText = __('CMS Pages');
        $this->_addButtonLabel = __('Add New Page');
        parent::__construct();
    }

}
