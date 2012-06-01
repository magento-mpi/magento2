<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Builder_Command_Create extends Mage_Backend_Model_Menu_Builder_CommandAbstract
{
    protected $_requiredParams = array(
        "id",
    );

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
