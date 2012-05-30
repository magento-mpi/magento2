<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Builder_Command_Add extends Mage_Backend_Model_Menu_Builder_CommandAbstract
{
    protected $_data = array(
        "id",
        "title",
        "module",
        "sort_order",
        "action"
    );

   /**
     * Add misssing data to item
     *
     * @param Mage_Backend_Model_Menu_Item $item
     */
    protected function _execute(Mage_Backend_Model_Menu_Item $item)
    {
        $item->addData($this->_data);
    }
}
