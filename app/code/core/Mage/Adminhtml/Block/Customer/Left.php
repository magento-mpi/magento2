<?php
/**
 * admin customer left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Left extends Mage_Adminhtml_Block_Widget_Accordion 
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    protected function _beforeToHtml()
    {
        $items = array();
    }
}
