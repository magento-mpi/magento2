<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Builder command to add root menu item
 */
class Mage_Backend_Model_Menu_Builder_Command_Create extends Mage_Backend_Model_Menu_Builder_CommandAbstract
{
    /**
     * Create root element
     *
     * @param array $itemParams
     * @return array
     */
    protected function _execute(array $itemParams)
    {
        foreach($this->_data as $key => $value) {
            $itemParams[$key] = $value;
        }
        return $itemParams;
    }
}
