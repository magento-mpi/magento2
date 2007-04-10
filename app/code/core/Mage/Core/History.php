<?php
/**
 * Data change history
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_History
{
    public static function addHistory()
    {
        $model = Mage::getModel('core', 'history');
    }
}