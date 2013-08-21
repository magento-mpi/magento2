<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Command to update menu item data
 */
class Magento_Backend_Model_Menu_Builder_Command_Update extends Magento_Backend_Model_Menu_Builder_CommandAbstract
{
    /**
     * Update item data
     *
     * @param array $itemParams
     * @return array
     */
    protected function _execute(array $itemParams)
    {
        foreach ($this->_data as $key => $value) {
            $itemParams[$key] = $value;
        }
        return $itemParams;
    }
}
