<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Factory
{
    /**
     * Retrieve menu object
     *
     * @param array $arguments
     * @return false|Mage_Core_Model_Abstract
     */
    public function getMenuInstance(array $arguments = array())
    {
        return Mage::getModel('Mage_Backend_Model_Menu', $arguments);
    }

    /**
     * Retrieve menu item object
     *
     * @param array $arguments
     * @return false|Mage_Core_Model_Abstract
     */
    public function getMenuItemInstance(array $arguments = array())
    {
        return Mage::getModel('Mage_Backend_Model_Menu_Item', $arguments);
    }

    /**
     * Retrieve menu logger object
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getLoggerInstance()
    {
        return Mage::getSingleton('Mage_Backend_Model_Menu_Logger');
    }
}
