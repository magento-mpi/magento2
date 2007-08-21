<?php
/**
 * Adminhtml popular tags report blocks content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com> 
 */

class Mage_Adminhtml_Block_Report_Tag_Popular extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'report_tag_popular';
        $this->_headerText = __('Popular Tags');
        parent::__construct();
        $this->_removeButton('add');
    }

}
