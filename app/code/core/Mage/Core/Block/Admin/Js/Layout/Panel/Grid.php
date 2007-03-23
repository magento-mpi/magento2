<?php
/**
 * Grid panel block
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Moshe Gurvich
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Admin_Js_Layout_Panel_Grid extends Mage_Core_Block_Admin_Js_Layout_Panel
{
    function construct($container='', $config=array())
    {
        parent::construct($container, $config);
        
        $this->setAttribute('jsClassName', 'Ext.GridPanel');
    }
}