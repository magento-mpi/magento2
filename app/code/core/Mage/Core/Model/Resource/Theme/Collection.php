<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme collection
 */
class Mage_Core_Model_Resource_Theme_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Theme', 'Mage_Core_Model_Resource_Theme');
    }

    /**
     * Collection after load
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _afterLoad()
    {
        /** @var $item Mage_Core_Model_Theme */
        foreach ($this->getItems() as $item) {
            $item->loadPackageData();
        }

        return parent::_afterLoad();
    }
}
